<?php
include_once("design.php");
$get_maps = mysqli_query($connection,"SELECT map_id,name as map,count(map_id) count,count(case when state=0 then state end) unconfirmed,count(case when state=1 then state end) confirmed FROM `bugs` JOIN maps ON bugs.map_id=maps.id JOIN users ON bugs.user_id=users.user_id WHERE ban=0 AND state<2 GROUP BY map_id");
?>
<div class='container' style='padding-bottom:100px;'>
				<div class='row'>
					<div class='col-md-8 col-md-offset-2'>
						<h2 class="text-center">Bug statistics</h2>
						<table class='table table-striped'>
							<thead>
							<tr><th>Map</th><th>All</th><th>Unconfirmed</th><th>Confirmed</th></tr>
							</thead>
							<tbody>
								<?php
								while ($r=mysqli_fetch_array($get_maps)) {
									echo "<tr><td><a href='index.php?map=".$r["map_id"]."'>".$r["map"]."</a></td><td>".$r["count"]."</td><td>".$r["unconfirmed"]."</td><td>".$r["confirmed"]."</td></tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
</div>
<?php
include_once("design2.php");
?>