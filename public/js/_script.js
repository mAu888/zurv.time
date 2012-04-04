(function(window, document, $, undefined) {
	var months = ['Jan', 'Feb', 'MŠr', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sept', 'Okt', 'Nov', 'Dez'];
	
	var Track = Spine.Model.sub();
	
	Track.configure('Track', 'description', 'date', 'minutes', 'rate', 'paid', 'project');
	
	Track.extend(Spine.Model.Ajax);
	Track.extend({
		url: 'tracks',
		
		dateSort: function(a, b) {
			var aPaid = a.paid,
				bPaid = b.paid,
				a = new Date(parseInt(a.date, 10)),
				b = new Date(parseInt(b.date, 10));
			
			if(a.getDate() === b.getDate() && a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear()) {
				if(aPaid === bPaid) return 0;
				else if(aPaid < bPaid) return 1;
				else return -1;
			}
			else if(a.getTime() < b.getTime()) {
				return -1;
			}
			else {
				return 1;
			}
		},
		
		fetch: function(params) {
			if(typeof params !== 'undefined' && params.project) {
				params.url = 'projects/' + params.project + '/tracks';
			}
			
			this.__super__.constructor.fetch.call(this, params);
		}
	});
	
	Track.include({
		sum: function(asNumber) {
			var sum = (this.minutes / 60) * this.rate;
			
			if(typeof asNumber === 'undefined' || ! asNumber) {
				return sum.toFixed(2);
			}
			else {
				return sum;
			}
		},
		
		formattedTime: function() {
			var minutes = this.minutes;
			
			var hours = Math.floor(minutes/60);
			minutes -= hours * 60;
			
			var s = '';
			if(hours > 0) {
				s += hours + 'h ';
			}
			
			if(minutes > 0) {
				s += minutes + 'm';
			}
			
			return s;
		},
		
		formattedDate: function(format) {
			var date = new Date(parseInt(this.date, 10));
			
			if(typeof format === 'undefined' || format === 'd-m') {
				return date.getDate() + '. ' + months[date.getMonth()];
			}
			else if(format === 'y-m-d') {
				return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
			}
		}
	});
	
	
	/**
	 * Project model
	 */
	var Project = Spine.Model.sub();
	
	Project.configure('Project', 'name');
	
	Project.extend(Spine.Model.Ajax);
	Project.extend({
		url: 'projects'
	});
	
	
	

	
	var Projects = Spine.Controller.sub({
		_current: null,
		
		el: $('#project-navigation'),
		
		elements: {
			'select:first': 'select',
			'#projects': 'projects'
		},
		
		events: {
			'change select:first': 'switchProject'
		},
		
		init: function() {
			Project.bind('create', this.proxy(this.addOne));
			Project.bind('change refresh', this.proxy(this.addAll));
			
			Project.fetch();
		},
		
		addOne: function(project) {
			this.projects.append($('#tmpl-project-item').jqote(project));
		},
		
		addAll: function() {
			// Remove all but first option (to show all tracks)
			this.projects.find('option').remove();
			
			Project.each(this.proxy(this.addOne));
		},
		
		addProject: function(name) {
			if(typeof name !== 'string')
			{
				do {
					name = prompt('Bitte ein Projektnamen eingeben:');
				} while(Project.findAllByAttribute('name', name).length > 0);
			}
			
			// If user aborts prompt dialog, name equals false
			if(name) {
				Project.create({ name: name });
			}
		},
		
		switchProject: function() {
			if(this.select.val() === 'new') {
				var name = '';
				do {
					name = prompt('Bitte ein Projektnamen eingeben:');
				} while(Project.findAllByAttribute('name', name).length > 0);
				
				if(name) {
					Project.create({ name: name });
				}
				
				this.select.val('-1');
			}
			
			this._current = this.select.val();
			this.trigger('change');
		},
		
		current: function() {
			if(isNaN(this._current) || this._current > 0) {
				return this._current;
			}
			else {
				return null;
			}
		}
	});
	
	
	
	/**
	 * AppController
	 */
	var TracksApp = Spine.Controller.sub({
		_projects: null,
		
		events: {
			'click #add-track-link': 'add',
			'click #filter-today': 'filterToday',
			'click #filter-month': 'filterMonth', 
			'click #filter-date': 'showDatePicker',
			'submit #new-track-entry': 'create',
			'reset #new-track-entry': 'reset'
		},
		
		elements: {
			'.time-track tbody': 'items',
			'span.paid': 'sumPaid',
			'span.total': 'sumTotal',
			'#projects': 'projects',
			'#add-track-link': 'add',
			'#filter-today': 'today',
			'#filter-month': 'month',
			'#filter-date': 'date',
			'#filter-date-input': 'dateInput'
		},
		
		init: function() {
			this._projects = new Projects();
			
			Track.bind('change refresh', this.proxy(this.calcTotal));
			Track.bind('create', this.proxy(this.addOne));
			Track.bind('change refresh', this.proxy(this.switchProject));
			
			this._projects.bind('change', this.proxy(this.switchProject));
			this._projects.bind('change', this.proxy(this.calcTotal));
			
			this.dateInput.datepicker({ 
				changeMonth: true,
				changeYear: true,
				onSelect: this.proxy(this.filterDate)
			});
			
			Track.fetch();// : Done by project change()
			
			//this.projects.change();
		},
		
		getTracks: function() {
			var tracks,
				project = this._projects.current();
			
			if(this.today.hasClass('active') || this.date.hasClass('active')) {
				var filter = ! this.today.hasClass('active') ? this.dateInput.datepicker('getDate') : new Date();
				
				tracks = Track.select(function(track) {
					var date = new Date(parseInt(track.date, 10));
					
					return  date.getDate() === filter.getDate() &&
							date.getMonth() === filter.getMonth() &&
							date.getFullYear() === filter.getFullYear() &&
							(! project || project === track.project);
				});
			}
			else if(this.month.hasClass('active')) {
				var filter = new Date();
				
				tracks = Track.select(function(track) {
					var date = new Date(parseInt(track.date, 10));
					
					return  date.getMonth() === filter.getMonth() &&
							date.getFullYear() === filter.getFullYear() &&
							(! project || project === track.project);
				});
			}
			else if (project){
				tracks = Track.findAllByAttribute('project', project);
			}
			else {
				tracks = Track.all();
			}
			
			tracks = tracks.sort(Track.dateSort);
			
			return tracks;
		},
		
		filterToday: function(e) {
			e.preventDefault();
			
			if(this._projects.current()) { 
				this.add.show();
			}

			this.today.addClass('active');
			this.month.removeClass('active');
			this.date.removeClass('active');
			this.dateInput.datepicker('setDate', null);
			this.addAll(this.getTracks());
			this.calcTotal();
		},
		
		filterMonth: function(e) {
			e.preventDefault();
			
			this.add.hide();
			
			this.month.addClass('active');
			this.date.removeClass('active');
			this.today.removeClass('active');
			
			this.addAll(this.getTracks());
			this.calcTotal();
		},
		
		filterDate: function(e) {
			if(this.dateInput.val() === '') {
				this.date.removeClass('active');
			}
			
			this.addAll(this.getTracks());
			this.calcTotal();
		},
		
		showDatePicker: function(e) {
			e.preventDefault();
			
			this.date.addClass('active');
			this.month.removeClass('active');
			this.today.removeClass('active');
			
			this.dateInput.datepicker('show');
		},
		
		switchProject: function() {
			if(! this._projects.current() || this.month.hasClass('active')) {
				this.add.hide();
			}
			else {
				this.add.show();
			}

			this.addAll(this.getTracks());
		},
		
		add: function() {
			if($('#new-track').size() < 1) {
				this.items.prepend($('#tmpl-new-track').jqote());
			}	
		},
		
		addOne: function(track) {
			var view = new Tracks({ item: track });
			
			view.render();
			
			this.items.prepend(view.el);
		},
		
		addAll: function(tracks) {
			var tracks = tracks || Track.all();
			
			this.items.empty();
			
			for(var i in tracks) {
				(this.proxy(this.addOne))(tracks[i]);
			}
		},
		
		calcTotal: function() {
			var tracks = this.getTracks(),
				total = 0,
				paid = 0;
			for(var i in tracks) {
				total += tracks[i].sum(true);
				
				if(tracks[i].paid == 1) {
					paid += tracks[i].sum(true);
				}
			}
			
			this.sumPaid.text(paid.toFixed(2));
			this.sumTotal.text(total.toFixed(2));
			
			if(paid === total) {
				this.sumTotal.hide();
			}
			else {
				this.sumTotal.show();
			}
		},
		
		reset: function(e) {
			e.preventDefault();
			
			alert('reset');
		},
		
		create: function(e, params) {
			e.preventDefault();
			
			this.id = $('#form-id');
			this.description = $('#form-description');
			this.rate = $('#form-rate');
			this.date = $('#form-date');
			this.minutes = $('#form-time');
			this.paid = $('#form-paid');
			
			var errors = false,
				reset = typeof params !== "undefined" && params.reset,
				project = this._projects.current(),
				rate = this.rate.val().replace(',', '.'),
				time = this.minutes.val();
			if(! reset && ! /^(([1-9]+[0-9]*)|([1-9]+[0-9]*.[0-9]{1,2}))$/.test(rate)) {
				errors = true;
				this.rate.addClass('error').one('click focus', function() { $(this).removeClass('error'); });
			}
			
			if(! reset && ! /^([1-9]+[0-9]*)$/.test(time)) {
				errors = true;
				this.minutes.addClass('error').one('click focus', function() { $(this).removeClass('error'); });
			}
			
			if(errors) {
				return;
			}
			
			var attributes = {
					description: this.description.val(),
					rate: rate,
					minutes: time,
					date: 	(this.date.val() !== '' ?  new Date(parseInt(this.date.val(), 10)) :
													(this.dateInput.datepicker('getDate') ? this.dateInput.datepicker('getDate') :
																							new Date())
							).getTime(),
					paid: this.paid.val() !== '' ? true : false
				};
			
			if(project) {
				attributes.project = project;
			}
			
			if(typeof params !== "undefined" && params.reset) {
				attributes = {};
			}
			
			if(this.id.val() === '') {
				Track.create(attributes);
				
				$('#new-track').remove();
			}
			else {
				Track.update(this.id.val(), attributes);
			}
		}
	});
	
	/**
	 * TracksController
	 */
	var Tracks = Spine.Controller.sub({
		events: {
			'change input[type="checkbox"]': 'toggle',
			'click .remove': 'remove',
			'dblclick': 'edit'
		},
		
		init: function(item) {
			this.newTrack = $('#input-fields');
			this.description = $('#form-description');
			this.rate = $('#form-rate');
			this.minutes = $('#form-time');
			this.date = $('#form-date');
			this.paid = $('#form-paid');
			this.id = $('#form-id');
			
			this.item.bind('change', this.proxy(this.render));
			//this.item.bind('destroy', this.proxy(this.destroy));
		},
	
		create: function(e) { 
			e.preventDefault();
		},
		
		render: function(item) {
			this.replace(this.el.jqotesub('#tmpl-track-item', item || this.item).find('tr'));
			
			return this;
		},
		
		remove: function() {
			this.el.remove();
			this.item.destroy();
		},
		
		toggle: function(e) {
			this.item.paid = this.item.paid == 0 ? 1 : 0;
			this.item.save();
			
			var that = this;
			that.el.addClass('h');
			window.setTimeout(function() { that.el.removeClass('h'); }, 2000);
		},
		
		edit: function(e) {
			// Do not allow double editing of same entry
			if(this.el.attr('id') === 'new-track') {
				return;
			}
			
			if($('#new-track').size() > 0) {
				$('#new-track-entry').trigger('submit', { reset: true });
			}
			
			this.newTrack = this.el.jqotesub('#tmpl-new-track', {
				id: this.item.id,
				description: this.item.description,
				rate: this.item.rate,
				date: this.item.date,
				time: this.item.minutes,
				paid: this.item.paid
			}).find('tr');
			
			this.replace(this.newTrack);
		}
	});
	
	$(document).ready(function() {
		var app = new TracksApp({ el: $('#content') });
		
		return app;
	});
	
})(window, document, jQuery);