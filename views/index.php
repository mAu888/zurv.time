<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="http://mauricio.local/zurv.time/public/">
    <meta charset="utf-8">
    <title>zurv.time - a minimalistic approach to time management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A minimalistic approach to time management">
    <meta name="author" content="Maurício Hanika">

    <!-- Le styles -->
    <link href="css/styles.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <!-- <link href="css/responsive.css" rel="stylesheet"> -->

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>

    <div class="container">
      <div class="row">
        <div class="span4">
          <h3>zurv.time</h3>
        </div>
        <div class="span8">
          <ul class="nav main-nav pull-right">
            <li <?php echo $this->isCurrentPage('customers', 'index') ? 'class="active"' : ''; ?>><a href="customers">Kunden</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Projekte<b class="caret"></b></a>
              <ul class="dropdown-menu span3">
                <?php foreach($this->customerList() as $customer): ?>
                  <?php if($customer->hasProjects()): ?>
                    <li class="nav-header"><?php echo $customer; ?></li>
                    <?php foreach($customer->getProjects() as $project): ?>
                      <li><a href="project/<?php echo $project->getId(); ?>"><?php echo $project; ?></a></li>
                    <?php endforeach; ?>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports<b class="caret"></b></a>
              <ul class="dropdown-menu pull-right">
                <li><a href="reports">Übersicht</a></li>
                <li class="divider"></li>
                <li><a href="reports/today">Heute</a></li>
                <li><a href="reports/current-week">Diese Woche</a></li>
                <li><a href="reports/current-month">Dieser Monat</a></li>
                <li class="divider"></li>
                <li><a href="reports/last-week">Letzte Woche</a></li>
                <li><a href="reports/last-month">Letzter Monat</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>

      <div class="row">

        <?php echo $content; ?>

        <hr>

        <footer>
          <p>&copy; <a href="http://www.zurv.de">zurv webdevelopment</a> 2012</p>
        </footer>
      </div>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/script.js"></script>

  </body>
</html>