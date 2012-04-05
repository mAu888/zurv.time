<div class="page-header">
  <h1>Kunden  <small>Verwalte alle Kunden</small></h1>
</div>

<div class="tabbable">
  <a href="#newCustomerModal" class="btn btn-primary pull-right" data-toggle="modal">Neuer Kunde</a>

  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#overview" data-toggle="tab">Übersicht</a>
    </li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane active" id="overview">
      <table class="table table-striped">
        <tbody>
          <?php foreach($customers as $customer): ?>
          <tr>
            <td><?php echo $customer; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="tab-pane" id="new">
      <p>Neukunde</p>
    </div>
  </div>
</div>

<!-- dialog -->
<div class="modal hide fade" id="newCustomerModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Neuer Kunde</h3>
  </div>
  <div class="modal-body">
    <p>One fine body…</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn">Schließen</a>
    <a href="#" class="btn btn-primary">Speichern</a>
  </div>
</div>
<!-- end dialog -->