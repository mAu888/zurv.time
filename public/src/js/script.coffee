formatDate = (d) ->
  d or= new Date()

  month = d.getMonth() + 1
  month = if month < 10 then "0" + month else "" + month

  day = d.getDate()
  day = if day < 10 then "0" + day else "" + day
  
  # return
  day + "." + month + "." + d.getFullYear()

# ----
# document.ready
# ----
$(document).ready ->
  $datepicker = $('div.date').datepicker()
  
  $datepicker.on 'changeDate', (e) ->
    $(this).datepicker 'hide'

  $('#modal-add-track').on 'show', (e) ->
    $datepicker.find('input').val(formatDate())

  $('#modal-add-track form').on 'submit', (e) ->
    e.preventDefault()

    $this = $ this

    description = $this.find('[name="description"]').val()
    rate = $this.find('[name="rate"]').val()
    minutes = $this.find('[name="minutes"]').val()
    paid = $this.find('[name="paid"]').prop 'checked'
    date = $this.find('[name="date"]').data('date-object').getTime()
    projectId = $(this).data 'project-id'

    $.ajax
      url: 'track'
      type: 'post'
      data:
        description: description
        rate: rate
        minutes: minutes
        paid: paid
        date: Math.round(date.getTime()/1000)
        projectId: projectId
      success: (response) =>
        console.log response
      complete:
        $(this).parent().modal 'hide'
