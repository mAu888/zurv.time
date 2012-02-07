(function() {
  var Project, Projects, Track, Tracks, TracksApp, isSameDay, isSameMonth, month,
    __hasProp = Object.prototype.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor; child.__super__ = parent.prototype; return child; },
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  month = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sept', 'Okt', 'Nov', 'Dez'];

  isSameDay = function(date1, date2) {
    return date1.getDate() === date2.getDate() && date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
  };

  isSameMonth = function(date1, date2) {
    return date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
  };

  $('#current-selected-month').text(month[(new Date()).getMonth()] + ' ' + (new Date()).getFullYear());

  /*
  MODELS
  */

  Track = (function(_super) {

    __extends(Track, _super);

    function Track() {
      Track.__super__.constructor.apply(this, arguments);
    }

    Track.configure('Track', 'description', 'date', 'minutes', 'rate', 'paid', 'project');

    Track.extend(Spine.Model.Ajax);

    Track.url = 'tracks';

    Track.dateSort = function(a, b) {
      var aDate, bDate;
      aDate = new Date(parseInt(a.date, 10));
      bDate = new Date(parseInt(b.date, 10));
      if (isSameDay(aDate, bDate)) {
        if (a.paid === b.paid) {
          if (aDate.getTime() === bDate.getTime()) {
            return 0;
          } else if (aDate.getTime() < bDate.getTime()) {
            return 1;
          } else {
            return -1;
          }
        } else if (a.paid < b.paid) {
          return -1;
        } else {
          return 1;
        }
      } else if (aDate.getTime() < bDate.getTime()) {
        return -1;
      } else {
        return 1;
      }
    };

    Track.prototype.fetch = function(params) {
      if ((params != null) && (params.project != null)) {
        params.url = "projects/" + params.project + "/tracks";
      }
      return Track.__super__.fetch.call(this, params);
    };

    Track.prototype.sum = function(asNumber) {
      var sum;
      sum = (this.minutes / 60) * this.rate;
      if ((asNumber != null) && !asNumber) sum.toFixed(2);
      return sum;
    };

    Track.prototype.formattedTime = function() {
      var hours, s;
      hours = Math.floor(this.minutes / 60);
      s = "";
      if (hours > 0) s = "" + hours + "h";
      if (this.minutes - hours * 60 > 0) {
        s = "" + s + " " + (this.minutes - hours * 60) + "m";
      }
      return s;
    };

    Track.prototype.formattedDate = function() {
      var date;
      date = new Date(parseInt(this.date, 10));
      if (!(typeof format !== "undefined" && format !== null) || format === 'd-m') {
        return "" + (date.getDate()) + ". " + month[date.getMonth()];
      } else if (format === 'y-m-d') {
        return "" + (date.getFullYear()) + "-" + (date.getMonth() + 1) + "-" + (date.getDate());
      }
    };

    Track.prototype.industryTime = function() {
      var hours;
      hours = this.minutes / 60;
      return (hours + ((this.minutes - 60 * hours) / 60 * 100)).toFixed(2);
    };

    return Track;

  })(Spine.Model);

  Project = (function(_super) {

    __extends(Project, _super);

    function Project() {
      Project.__super__.constructor.apply(this, arguments);
    }

    Project.configure('Project', 'name');

    Project.extend(Spine.Model.Ajax);

    Project.url = 'projects';

    return Project;

  })(Spine.Model);

  /*
  CONTROLLERS
  */

  Projects = (function(_super) {

    __extends(Projects, _super);

    Projects.prototype._current = null;

    Projects.prototype.el = $('#project-navigation');

    Projects.prototype.elements = {
      'select:first': 'select',
      '#projects': 'projects'
    };

    Projects.prototype.events = {
      'change select:first': 'change'
    };

    Projects.prototype.templates = {
      'item': $('#tmpl-project-item')
    };

    function Projects() {
      this.addAll = __bind(this.addAll, this);
      this.addOne = __bind(this.addOne, this);      Projects.__super__.constructor.apply(this, arguments);
      Project.bind('change refresh', this.addAll);
      Project.fetch();
    }

    Projects.prototype.addOne = function(project) {
      return this.projects.append(this.templates.item.jqote(project));
    };

    Projects.prototype.addAll = function(project) {
      this.projects.find('option').remove();
      Project.each(this.addOne);
      if (project != null) return this.select.val(project.id).change();
    };

    Projects.prototype.change = function() {
      var name;
      switch (this.select.val()) {
        case 'new':
          name = "";
          while (name === '' || Project.findAllByAttribute('name', name).length > 0) {
            name = prompt('Projektname');
          }
          if (name) {
            Project.create({
              'name': name
            });
          }
          this.select.val('-1');
      }
      this._current = this.select.val();
      return this.trigger('change');
    };

    Projects.prototype.remove = function() {
      return Project.destroy(this._current);
    };

    Projects.prototype.current = function() {
      var current;
      return current = (this._current != null) && this._current > 0 ? this._current : null;
    };

    return Projects;

  })(Spine.Controller);

  /*
  TracksController
  */

  Tracks = (function(_super) {

    __extends(Tracks, _super);

    Tracks.prototype.events = {
      'change input[type="checkbox"]': 'toggle',
      'click .remove': 'remove',
      'dblclick': 'edit'
    };

    function Tracks(item) {
      this.edit = __bind(this.edit, this);
      this.toggle = __bind(this.toggle, this);
      this.remove = __bind(this.remove, this);
      this.render = __bind(this.render, this);      Tracks.__super__.constructor.apply(this, arguments);
      this.item.bind('change', this.render);
    }

    Tracks.prototype.render = function(item) {
      this.replace(this.el.jqotesub('#tmpl-track-item', this.item).find('tr'));
      return this;
    };

    Tracks.prototype.remove = function() {
      this.el.remove();
      return this.item.destroy();
    };

    Tracks.prototype.toggle = function() {
      this.item.paid = parseInt(this.item.paid, 10) === 0 || !this.item.paid ? 1 : 0;
      return this.item.save();
    };

    Tracks.prototype.edit = function() {
      var track;
      if (this.el.attr('id') === 'new-track') return;
      if ($('#new-track').size() > 0) {
        $('#new-track-entry').trigger('submit', {
          reset: true
        });
      }
      track = this.el.jqotesub('#tmpl-new-track', {
        id: this.item.id,
        description: this.item.description,
        rate: this.item.rate,
        date: this.item.date,
        time: this.item.minutes,
        paid: this.item.paid
      }).find('tr');
      return this.replace(track);
    };

    return Tracks;

  })(Spine.Controller);

  /*
  ApplicationController
  */

  TracksApp = (function(_super) {

    __extends(TracksApp, _super);

    TracksApp.prototype._projectsController = null;

    TracksApp.prototype.lastSelection = null;

    TracksApp.prototype.events = {
      'click #add-track-link': 'add',
      'click #delete-project-link': 'deleteProject',
      'click #filter-today': 'filter',
      'click #filter-prev-month': 'filter',
      'click #filter-next-month': 'filter',
      'click #filter-month': 'filter',
      'click #filter-date': 'showDatePicker',
      'change #check-all-paid': 'setAllPaid',
      'submit #new-track-entry': 'create',
      'reset #new-track-entry': 'reset'
    };

    TracksApp.prototype.elements = {
      '.time-track tbody': 'items',
      'span.paid': 'sumPaid',
      'span.total': 'sumTotal',
      '#projects': 'projects',
      '#add-track-link': 'add',
      '#delete-project-link': 'delete',
      '#filter-today': 'today',
      '#filter-prev-month': 'prevMonth',
      '#filter-month': 'month',
      '#filter-date': 'date',
      '#current-selected-month': 'currentMonth',
      '#filter-date-input': 'dateInput',
      '#tmpl-new-track': 'tmplNewTrack',
      '#check-all-paid': 'allPaid'
    };

    function TracksApp() {
      this.switchProject = __bind(this.switchProject, this);
      this.setAllPaid = __bind(this.setAllPaid, this);
      this.calcTotal = __bind(this.calcTotal, this);
      this.deleteProject = __bind(this.deleteProject, this);
      this.addAll = __bind(this.addAll, this);
      this.addOne = __bind(this.addOne, this);
      this.add = __bind(this.add, this);
      this.filter = __bind(this.filter, this);
      var _this = this;
      TracksApp.__super__.constructor.apply(this, arguments);
      this._projectsController = new Projects();
      this._projectsController.bind('change', this.switchProject);
      this._projectsController.bind('change', this.calcTotal);
      Track.bind('change refresh', this.calcTotal);
      Track.bind('change refresh', this.switchProject);
      this.bind('change', this.calcTotal);
      Track.fetch();
      this.dateInput.datepicker({
        changeMonth: true,
        changeYear: true,
        onSelect: this.filter,
        onClose: function(dateText, inst) {
          if (dateText === '') {
            _this.date.removeClass('active');
            return _this.lastSelection.addClass('active');
          }
        }
      });
    }

    TracksApp.prototype.getTracks = function() {
      var dateFilter, project, tracks;
      project = this._projectsController.current();
      if (this.today.hasClass('active') || this.date.hasClass('active')) {
        dateFilter = this.today.hasClass('active') ? new Date() : this.dateInput.datepicker('getDate');
        tracks = Track.select(function(track) {
          var date;
          date = new Date(parseInt(track.date, 10));
          return isSameDay(date, dateFilter) && (!project || project === track.project);
        });
      } else if (this.month.hasClass('active') || this.prevMonth.hasClass('active')) {
        dateFilter = new Date();
        if (this.prevMonth.hasClass('active')) {
          dateFilter.setMonth(dateFilter.getMonth() - 1);
        }
        tracks = Track.select(function(track) {
          var date;
          date = new Date(parseInt(track.date, 10));
          return isSameMonth(date, dateFilter) && (!project || project === track.project);
        });
      } else if (this.currentMonth.hasClass('active')) {
        dateFilter = this.currentMonth.data('date');
        tracks = Track.select(function(track) {
          var date;
          date = new Date(parseInt(track.date, 10));
          return isSameMonth(date, dateFilter) && (!project || project === track.project);
        });
      } else if (project) {
        tracks = Track.findAllByAttribute('project', project);
      } else {
        tracks = Track.all();
      }
      return tracks.sort(Track.dateSort);
    };

    TracksApp.prototype.filter = function(e) {
      var _ref;
      if (e != null) {
        if (typeof e.preventDefault === "function") e.preventDefault();
      }
      this.today.removeClass('active');
      this.currentMonth.removeClass('active');
      this.prevMonth.removeClass('active');
      this.month.removeClass('active');
      this.date.removeClass('active');
      if (typeof this.currentMonth.data('date') === "undefined") {
        this.currentMonth.data('date', new Date());
      }
      switch (((_ref = e.srcElement) != null ? _ref.id : void 0)) {
        case 'filter-today':
          this.today.addClass('active');
          this.currentMonth.data('date', new Date());
          break;
        case 'filter-prev-month':
          this.currentMonth.addClass('active').data('date').setMonth(this.currentMonth.data('date').getMonth() - 1);
          break;
        case 'filter-next-month':
          this.currentMonth.addClass('active').data('date').setMonth(this.currentMonth.data('date').getMonth() + 1);
          break;
        case 'filter-month':
          this.currentMonth.data('date', new Date()).addClass('active');
          break;
        default:
          this.date.addClass('active');
      }
      this.currentMonth.text(month[this.currentMonth.data('date').getMonth()] + ' ' + this.currentMonth.data('date').getFullYear());
      return this.addAll(this.getTracks());
    };

    TracksApp.prototype.add = function(e) {
      var form;
      e.preventDefault();
      form = $('#tmpl-new-track').jqote();
      return this.items.prepend(form);
    };

    TracksApp.prototype.addOne = function(track) {
      return this.items.prepend(new Tracks({
        item: track
      }).render().el);
    };

    TracksApp.prototype.addAll = function(tracks) {
      var track, _i, _len;
      if (tracks == null) tracks = Track.all();
      this.items.empty();
      for (_i = 0, _len = tracks.length; _i < _len; _i++) {
        track = tracks[_i];
        this.addOne(track);
      }
      return this.trigger('change');
    };

    TracksApp.prototype.deleteProject = function(e) {
      e.preventDefault();
      return this._projectsController.remove();
    };

    TracksApp.prototype.calcTotal = function() {
      var paid, total, track, tracks, _i, _len;
      tracks = this.getTracks();
      total = 0;
      paid = 0;
      for (_i = 0, _len = tracks.length; _i < _len; _i++) {
        track = tracks[_i];
        total += track.sum(true);
        if (track.paid && track.paid !== '0') paid += track.sum(true);
      }
      this.sumPaid.text(paid.toFixed(2));
      this.sumTotal.text(total.toFixed(2));
      if (total === paid) {
        this.sumTotal.hide();
        return this.allPaid.prop('checked', true);
      } else {
        this.sumTotal.show();
        return this.allPaid.prop('checked', false);
      }
    };

    TracksApp.prototype.setAllPaid = function() {
      var checked, track, _i, _len, _ref;
      checked = this.allPaid.is(':checked');
      _ref = this.getTracks();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        track = _ref[_i];
        track.paid = checked ? 1 : 0;
        track.save();
      }
      return this.allPaid.attr('checked', checked);
    };

    TracksApp.prototype.switchProject = function() {
      var project;
      project = this._projectsController.current();
      if (project && project !== 'new') {
        this.add.show();
        this["delete"].show();
      } else {
        this.add.hide();
        this["delete"].hide();
      }
      return this.addAll(this.getTracks());
    };

    TracksApp.prototype.showDatePicker = function(e) {
      e.preventDefault();
      this.lastSelection = this.month.hasClass('active') ? this.month : this.today;
      this.date.addClass('active');
      this.month.removeClass('active');
      this.today.removeClass('active');
      return this.dateInput.datepicker('show');
    };

    TracksApp.prototype.create = function(e, params) {
      var attributes, date, description, errors, id, match, minutes, minutesVal, paid, project, rate, rateVal, reset;
      e.preventDefault();
      id = $('#form-id');
      description = $('#form-description');
      rate = $('#form-rate');
      date = $('#form-date');
      minutes = $('#form-time');
      paid = $('#form-paid');
      errors = false;
      reset = (params != null) && params.reset;
      project = this._projectsController.current();
      rateVal = rate.val().replace(',', '.');
      minutesVal = minutes.val();
      if (/^([1-9]+[0-9]*):([0-5]?[0-9])$/.test(minutesVal)) {
        match = minutesVal.match(/^([1-9]+[0-9]*):([0-5]?[0-9])$/);
        minutesVal = parseInt(match[1], 10) * 60 + parseInt(match[2], 10);
      }
      if (!reset && !/^(([1-9]+[0-9]*)|([1-9]+[0-9]*.[0-9]{1,2}))$/.test(rateVal)) {
        errors = true;
        rate.addClass('error').one('click focus', function() {
          return $(this).removeClass('error');
        });
      }
      if (!reset && !/^([1-9]+[0-9]*)$/.test(minutesVal)) {
        errors = true;
        minutes.addClass('error').one('click focus', function() {
          return $(this).removeClass('error');
        });
      }
      if (errors) return;
      attributes = {};
      if (!(params != null) || !params.reset) {
        attributes = {
          description: description.val(),
          rate: rateVal,
          minutes: minutesVal,
          date: (date.val() !== '' ? new Date(parseInt(date.val(), 10)) : this.dateInput.datepicker('getDate') ? this.dateInput.datepicker('getDate') : new Date()).getTime(),
          paid: paid.val() !== ''
        };
      }
      if (project) attributes.project = project;
      if (id.val() === '') {
        Track.create(attributes);
        return $('#new-track').remove();
      } else {
        return Track.update(id.val(), attributes);
      }
    };

    return TracksApp;

  })(Spine.Controller);

  new TracksApp({
    el: $('#content')
  });

}).call(this);
