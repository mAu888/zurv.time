<table class="table table-striped">
  <thead>
    <tr>
      <th class="span1 text-center">Datum</th>
      <th class="span5">Beschreibung</th>
      <th class="span1 text-center">Stundensatz</th>
      <th class="span1 text-center">Dauer</th>
      <th class="span1 text-center">Gezahlt</th>
      <th class="span3">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php $lastTrack = null; foreach($tracks as $track): ?>
      <?php if(! $track->isSameDay($lastTrack)): ?>
      <tr>
        <th colspan="6"><?php echo $track->getDateFormatted(); ?></th>
      </tr>
      <?php endif; ?>
      <tr> 
        <td class="text-center"><!--<?php echo $track->getDateFormatted(); ?>--></td>
        <td><?php echo $track->getDescription(); ?></td>
        <td class="text-center"><?php echo $track->getRate(); ?> &euro;</td>
        <td class="text-center"><?php echo $track->getMinutes(); ?></td>
        <td class="text-center"><?php echo $track->getPaid(); ?></td>
        <td class="text-right"><?php echo $track->getDeleted(); ?></td>
      </tr>
    <?php $lastTrack = $track; endforeach; ?>
    <?php if(empty($tracks)): ?>
    <tr>
      <td colspan="6">Noch keine Zeiteinträge</td>
    </tr>
    <?php endif; ?>
  </tbody>
</table>