(function() {
  var formatDate;

  formatDate = function(d) {
    var day, month;
    d || (d = new Date());
    month = d.getMonth() + 1;
    month = month < 10 ? "0" + month : "" + month;
    day = d.getDate();
    day = day < 10 ? "0" + day : "" + day;
    return day + "." + month + "." + d.getFullYear();
  };

  $(document).ready(function() {
    var $datepicker, reset;
    $datepicker = $('div.date').datepicker();
    $datepicker.on('changeDate', function(e) {
      return $(this).datepicker('hide');
    });
    reset = function($this) {
      $this.find('[name="description"]').val('');
      $this.find('[name="rate"]').val('');
      $this.find('[name="minutes"]').val('');
      $this.find('[name="paid"]').prop('checked', false);
      $this.find('[name="date"]').val('');
      return $datepicker.find('input').val(formatDate());
    };
    $('#modal-add-track').on('show', function(e) {
      return $datepicker.find('input').val(formatDate());
    });
    $('#modal-add-track').on('hide', function(e) {
      return reset($(this));
    });
    return $('#modal-add-track form').on('submit', function(e) {
      var $this, date, description, minutes, paid, projectId, rate,
        _this = this;
      e.preventDefault();
      $this = $(this);
      description = $this.find('[name="description"]').val();
      rate = $this.find('[name="rate"]').val();
      minutes = $this.find('[name="minutes"]').val();
      paid = $this.find('[name="paid"]').prop('checked');
      date = $this.find('[name="date"]').val();
      projectId = $(this).data('project-id');
      return $.ajax({
        url: 'track',
        type: 'post',
        data: {
          description: description,
          rate: rate,
          minutes: minutes,
          paid: paid,
          date: date,
          projectId: projectId
        },
        success: function(response) {
          return console.log(response);
        },
        complete: function() {
          reset($this);
          return $(_this).parent().modal('hide');
        }
      });
    });
  });

}).call(this);
