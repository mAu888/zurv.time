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

  reset = ($this) ->
    $this.find('[name="description"]').val('')
    $this.find('[name="rate"]').val('')
    $this.find('[name="minutes"]').val('')
    $this.find('[name="paid"]').prop 'checked', false
    $this.find('[name="date"]').val('')
    $datepicker.find('input').val(formatDate())

  $('#modal-add-track').on 'show', (e) ->
    $datepicker.find('input').val(formatDate())

  $('#modal-add-track').on 'hide', (e) ->
    reset $(this)

  $('#modal-add-track form').on 'submit', (e) ->
    e.preventDefault()

    $this = $ this

    description = $this.find('[name="description"]').val()
    rate = $this.find('[name="rate"]').val()
    minutes = $this.find('[name="minutes"]').val()
    paid = $this.find('[name="paid"]').prop 'checked'
    date = $this.find('[name="date"]').val()
    projectId = $(this).data 'project-id'

    $.ajax
      url: 'track'
      type: 'post'
      data:
        description: description
        rate: rate
        minutes: minutes
        paid: paid
        date: date
        projectId: projectId
      success: (response) =>
        console.log response
      complete: () =>
        reset $this
        $(this).parent().modal 'hide'
