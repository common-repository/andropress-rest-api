<?php
/**
*Plugin Menu Page
*/
defined('ABSPATH') or die('you can not access this file !');
?>

<div class="menu-page container">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#generateNew" data-toggle="tab">Generate New</a></li>
		<li><a href="#myApps" data-toggle="tab">My Apps</a></li>
	</ul>

	<div class="tab-content col-sm-8">
		<div id="generateNew" class="tab-pane fade in active">
			<h3 class="result"></h3>
			<form class="form-horizontal app-form" role="form" method="POST" action="">
				<input type="hidden" name="action" value="insert_app">
				<div class="form-group">
					<lable for="app-name" class="col-sm-2">App Name</lable>
					<div class="col-sm-10">
						<input type="text" required="required" name="app-name" id="app-name" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<lable for="app-key" class="col-sm-2">App Key</lable>
					<div class="col-sm-8">
						<input type="text" required="required" name="app-key" id="app-key" class="form-control" disabled="disabled" required="required">
					</div>
					<div class="col-sm-2">
						<button type="button" id="btn-generate" class="btn btn-primary">Generate</button>
					</div>
				</div>
				<button type="button" id="btn-create" class="btn btn-success">Create</button>
			</form>
		</div>

		<div id="dialog" title="View App Key" class="clearfix"></div>

		<div id="myApps" class="tab-pane fade clearfix">
			<?php
				global $wpdb;
				$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."andropress_rest_api ORDER BY id DESC;");
				foreach($results as $result){ ?>
					<div class="col-sm-8">
						<button type="button" class="btn btn-info"><?php echo $result->app_name; ?></button>
					</div>
					<div class="col-sm-2">
						<button type="button" onclick="showKey('<?php echo $result->app_name; ?>', '<?php echo $result->key; ?>')" id="btn-view" class="btn btn-success">View Key</button>
					</div>
					<div class="col-sm-2">
						<button type="button" onclick="deleteApp(<?php echo $result->id; ?>)" id="btn-delete" class="btn btn-danger">Delete</button>
					</div>
		<?php }
			?>
		</div>
	</div>
</div>
