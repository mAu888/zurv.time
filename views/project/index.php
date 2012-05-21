<h2>
  <?php echo $project->getName(); ?> <small>Foo? Bar!</small>
</h2>

<a href="#modal-add-track" class="btn btn-primary pull-right" data-toggle="modal"><i class="icon-plus icon-white"></i></a>

<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#1" data-toggle="tab">Aktueller Monat</a></li>
    <li><a href="#2" data-toggle="tab">Alle</a></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane active" id="1">
      <?php echo $this->tracksTable($project->getTracks(Project::TRACKS_CURRENTMONTH)); ?>
    </div>
    <div class="tab-pane" id="2">
      <?php echo $this->tracksTable($project->getTracks()); ?>
    </div>
  </div>
</div>

<!-- add dialog -->
<div class="modal fade hide" id="modal-add-track">
  <form class="form-inline" method="post" data-project-id="<?php echo $project->getId(); ?>">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">×</a>
      <h3>Neuer Zeiteintrag</h3>
    </div>
    <div class="modal-body">
      <input type="text" class="input-xlarge" name="description" placeholder="Beschreibung">
      <div class="input-append">
        <input type="text" class="input-mini text-right" name="rate" placeholder="&euro; / h"><span class="add-on">&euro;</span>
      </div>
      <div class="input-append">
        <input type="text" class="input-mini text-right" name="minutes" placeholder="min"><span class="add-on">min</span>
      </div>
      <label class="checkbox">
        <input type="checkbox" name="paid"> Gezahlt
      </label>
      <div class="input-append date" id="dp3" data-date="<?php echo date('d.m.Y'); ?>" data-date-format="dd.mm.yyyy">
        <input class="input-small" size="16" type="text" name="date" value="<?php echo date('d.m.Y'); ?>"><span class="add-on"><i class="icon-th"></i></span>
      </div>
    </div>
    <div class="modal-footer">
      <a class="btn" data-dismiss="modal">Abbrechen</a>
      <button class="btn btn-primary">Speichern</button>
    </div>
  </form>
</div>
<!-- end add dialog -->