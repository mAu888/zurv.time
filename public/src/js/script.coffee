month = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sept', 'Okt', 'Nov', 'Dez']

isSameDay = (date1, date2) ->
    date1.getDate() is date2.getDate() and date1.getMonth() is date2.getMonth() and date1.getFullYear() is date2.getFullYear()
    
isSameMonth = (date1, date2) ->
    date1.getMonth() is date2.getMonth() and date1.getFullYear() is date2.getFullYear()

$('#current-selected-month').text(month[(new Date()).getMonth()] + ' ' + (new Date()).getFullYear())

###
MODELS
###
class Track extends Spine.Model
    @configure 'Track', 'description', 'date', 'minutes', 'rate', 'paid', 'project'
    @extend Spine.Model.Ajax
    
    @url: 'tracks'
    
    @dateSort: (a, b) ->
        aDate = new Date(parseInt(a.date, 10))
        bDate = new Date(parseInt(b.date, 10))
        
        if isSameDay(aDate, bDate)
            if(a.paid is b.paid)
                if aDate.getTime() is bDate.getTime()
                    0
                else if aDate.getTime() < bDate.getTime()
                    1
                else
                    -1
            else if a.paid < b.paid
                -1
            else
                1
        else if aDate.getTime() < bDate.getTime()
            -1
        else
            1
    
    fetch: (params) ->
        params.url = "projects/#{params.project}/tracks" if params? and params.project?
        
        super params

    sum: (asNumber) ->
        sum = (@minutes / 60) * @rate
        
        if asNumber? and not asNumber
            sum.toFixed(2)
        
        sum
        
    formattedTime: () ->
        hours = Math.floor(@minutes/60)
        
        s = ""
        if hours > 0
            s = "#{hours}h"
        
        if @minutes - hours * 60 > 0
            s = "#{s} #{@minutes - hours * 60}m"
        
        s
        
    formattedDate: () ->
        date = new Date(parseInt(@date, 10))
        
        if not format? or format == 'd-m'
            "#{date.getDate()}. #{month[date.getMonth()]}"
        else if format == 'y-m-d'
            "#{date.getFullYear()}-#{date.getMonth() + 1}-#{date.getDate()}"
            
    industryTime: () ->
        hours = @minutes/60
        (hours + ((@minutes - 60*hours)/60*100)).toFixed(2)


class Project extends Spine.Model
    @configure 'Project', 'name'
    @extend Spine.Model.Ajax
    
    @url: 'projects'
    

###
CONTROLLERS
###
class Projects extends Spine.Controller
    _current: null
    
    el: $('#project-navigation')
    
    elements:
        'select:first': 'select'
        '#projects': 'projects'

    events:
        'change select:first': 'change'
        
    templates:
        'item': $('#tmpl-project-item')
        
    constructor: ->
        super
        
        Project.bind('change refresh', @addAll)
        
        Project.fetch()
        
    addOne: (project) =>
        @projects.append(@templates.item.jqote(project))
        
    addAll: (project) =>
        @projects.find('option').remove()
        
        Project.each(@addOne)
        
        @select.val(project.id).change() if project?
    
    change: ->
        switch @select.val()
            when 'new'
                name = ""
                while name == '' or Project.findAllByAttribute('name', name).length > 0
                    name = prompt('Projektname')
                if name
                    Project.create({ 'name': name })
                
                @select.val('-1')
        
        @_current = @select.val()
        @trigger('change')
        
    remove: ->
        Project.destroy(@_current)
        
    current: ->
        current = if @_current? && @_current > 0 then @_current else null


###
TracksController
###
class Tracks extends Spine.Controller
    events:
        'change input[type="checkbox"]': 'toggle'
        'click .remove': 'remove'
        'dblclick': 'edit'

    constructor: (item) ->
        super
        
        @item.bind('change', @render)

    render: (item) =>
        @replace(@el.jqotesub('#tmpl-track-item', @item).find('tr'))
        @    

    remove: =>
        @el.remove()
        @item.destroy()
        
    toggle: =>
        @item.paid = if parseInt(@item.paid, 10) == 0 or ! @item.paid then 1 else 0
        @item.save()
        
    edit: =>
        return if @el.attr('id') == 'new-track'
        
        $('#new-track-entry').trigger('submit', { reset: true }) if $('#new-track').size() > 0
        
        track = this.el.jqotesub('#tmpl-new-track',
            id: @item.id
            description: @item.description
            rate:@item.rate
            date: @item.date
            time: @item.minutes
            paid: @item.paid
        ).find('tr')
        
        @replace(track)
        
        
     
###
ApplicationController
####   
class TracksApp extends Spine.Controller
    _projectsController: null
    
    lastSelection: null
    
    events:
        'click #add-track-link': 'add'
        'click #delete-project-link': 'deleteProject'
        'click #filter-today': 'filter'
        'click #filter-prev-month': 'filter'
        'click #filter-next-month': 'filter'
        'click #filter-month': 'filter' 
        'click #filter-date': 'showDatePicker'
        'change #check-all-paid': 'setAllPaid'
        'submit #new-track-entry': 'create'
        'reset #new-track-entry': 'reset'
        
    elements:
        '.time-track tbody': 'items'
        'span.paid': 'sumPaid'
        'span.total': 'sumTotal'
        '#projects': 'projects'
        '#add-track-link': 'add'
        '#delete-project-link': 'delete'
        '#filter-today': 'today'
        '#filter-prev-month': 'prevMonth'
        '#filter-month': 'month'
        '#filter-date': 'date'
        '#current-selected-month': 'currentMonth'
        '#filter-date-input': 'dateInput'
        '#tmpl-new-track': 'tmplNewTrack'
        '#check-all-paid': 'allPaid'
        
    constructor: ->
        super
        @_projectsController = new Projects()
        
        @_projectsController.bind('change', @switchProject)
        @_projectsController.bind('change', @calcTotal)
        
        Track.bind('change refresh', @calcTotal)
        Track.bind('change refresh', @switchProject)
        
        @bind('change', @calcTotal) 
        
        Track.fetch()
        
        @dateInput.datepicker
            changeMonth: true
            changeYear: true
            onSelect: @filter
            onClose: (dateText, inst) =>
                if dateText is ''
                    @date.removeClass('active')
                    @lastSelection.addClass('active')
            
    getTracks: ->
        project = @_projectsController.current()
        
        if @today.hasClass('active') or @date.hasClass('active')
            dateFilter = if @today.hasClass('active') then new Date() else @dateInput.datepicker('getDate')
            
            tracks = Track.select (track) ->
                date = new Date(parseInt(track.date, 10))
                
                isSameDay(date, dateFilter) and (! project or project is track.project)
        else if @month.hasClass('active') or @prevMonth.hasClass('active')
            dateFilter = new Date()
            
            if @prevMonth.hasClass('active')
                dateFilter.setMonth(dateFilter.getMonth() - 1)
            
            tracks = Track.select (track) ->
                date = new Date(parseInt(track.date, 10))
                
                isSameMonth(date, dateFilter) and (! project or project is track.project)
        else if @currentMonth.hasClass('active')
            dateFilter = @currentMonth.data('date')

            tracks = Track.select (track) ->
                date = new Date(parseInt(track.date, 10))
                
                isSameMonth(date, dateFilter) and (! project or project is track.project)
        else if project
            tracks = Track.findAllByAttribute('project', project)
        else
            tracks = Track.all()
            
        tracks.sort(Track.dateSort)
       
    filter: (e) =>
        e.preventDefault?() if e?
        
        @today.removeClass('active')
        @currentMonth.removeClass('active')
        @prevMonth.removeClass('active')  
        @month.removeClass('active')
        @date.removeClass('active')
        
        if typeof @currentMonth.data('date') is "undefined" then @currentMonth.data('date', new Date())

        switch(e.srcElement?.id)
            when 'filter-today'
                @today.addClass('active')
                @currentMonth.data('date', new Date())
            when 'filter-prev-month' then @currentMonth.addClass('active').data('date').setMonth(@currentMonth.data('date').getMonth() - 1)
            when 'filter-next-month' then @currentMonth.addClass('active').data('date').setMonth(@currentMonth.data('date').getMonth() + 1)
            when 'filter-month' then @currentMonth.data('date', new Date()).addClass('active')
            else @date.addClass('active')

        @currentMonth.text(month[@currentMonth.data('date').getMonth()] + ' ' + @currentMonth.data('date').getFullYear())

        @addAll(@getTracks())

    add: (e) =>
        e.preventDefault()
        
        form = $('#tmpl-new-track').jqote()
        @items.prepend(form)

    addOne: (track) =>
        @items.prepend new Tracks(item: track).render().el
        
    
    addAll: (tracks) =>
        tracks ?= Track.all()
        
        @items.empty()
        
        @addOne(track) for track in tracks 
        
        @trigger('change')        

    deleteProject: (e) =>
        e.preventDefault()
        
        @_projectsController.remove()

    calcTotal: =>
        tracks = @getTracks()
        total = 0
        paid = 0
        
        for track in tracks
            total += track.sum(true)
            paid += track.sum(true) if track.paid && track.paid != '0'
            
        @sumPaid.text(paid.toFixed(2))
        @sumTotal.text(total.toFixed(2))
        
        if total is paid
            @sumTotal.hide()
            @allPaid.prop('checked', true)
        else
            @sumTotal.show()
            @allPaid.prop('checked', false)

    setAllPaid: =>
        checked = @allPaid.is(':checked')
        
        for track in @getTracks()
            track.paid = if checked then 1 else 0
            track.save()
        @allPaid.attr('checked', checked)

    switchProject: =>
        project = @_projectsController.current()
        
        if project and project != 'new'
            @add.show()
            @delete.show()
        else
            @add.hide()
            @delete.hide()
            
        @addAll(@getTracks())
    
    showDatePicker: (e) ->
        e.preventDefault()
        
        @lastSelection = if @month.hasClass('active') then @month else @today
        
        @date.addClass('active')
        @month.removeClass('active')
        @today.removeClass('active')
        
        @dateInput.datepicker('show')
        
    create: (e, params) ->
        e.preventDefault()
        
        id = $('#form-id')
        description = $('#form-description')
        rate = $('#form-rate')
        date = $('#form-date')
        minutes = $('#form-time')
        paid = $('#form-paid')
        
        errors = false
        reset = params? and params.reset
        project = @_projectsController.current()
        rateVal = rate.val().replace(',', '.')
        minutesVal = minutes.val()
        
        if(/^([1-9]+[0-9]*):([0-5]?[0-9])$/.test(minutesVal))
            match = minutesVal.match(/^([1-9]+[0-9]*):([0-5]?[0-9])$/)
            minutesVal = parseInt(match[1], 10) * 60 + parseInt(match[2], 10)
        
        if ! reset and ! /^(([1-9]+[0-9]*)|([1-9]+[0-9]*.[0-9]{1,2}))$/.test(rateVal)
            errors = true
            rate.addClass('error').one('click focus', ->
                $(this).removeClass('error')
            )
            
        if ! reset and ! /^([1-9]+[0-9]*)$/.test(minutesVal)
            errors = true
            minutes.addClass('error').one('click focus', -> 
                $(this).removeClass('error')
            )
            
        return if errors
        
        attributes = {}
        
        if ! params? or ! params.reset
            attributes = 
                description: description.val()
                rate: rateVal
                minutes: minutesVal
                date: (if date.val() != '' then new Date(parseInt(date.val(), 10)) else if @dateInput.datepicker('getDate') then @dateInput.datepicker('getDate') else new Date()).getTime()
                paid: paid.val() != ''
                
        attributes.project = project if project
        
        if id.val() == ''
            Track.create(attributes)
            $('#new-track').remove()
        else
            Track.update(id.val(), attributes)
        

new TracksApp(el: $('#content'))