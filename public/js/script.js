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
    var $datepicker;
    $datepicker = $('div.date').datepicker();
    $datepicker.on('changeDate', function(e) {
      return $(this).datepicker('hide');
    });
    $('#modal-add-track').on('show', function(e) {
      return $datepicker.find('input').val(formatDate());
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
      date = $this.find('[name="date"]').data('date-object').getTime();
      projectId = $(this).data('project-id');
      return $.ajax({
        url: 'track',
        type: 'post',
        data: {
          description: description,
          rate: rate,
          minutes: minutes,
          paid: paid,
          date: Math.round(date.getTime() / 1000),
          projectId: projectId
        },
        success: function(response) {
          return console.log(response);
        },
        complete: $(this).parent().modal('hide')
      });
    });
  });

}).call(this);
