<?php
include_once("design.php");
$get_moderators = mysqli_query($connection,"SELECT steam_id,steam_avatar,steam_persona FROM users WHERE rank=1");
?>
<div class='container' style='padding-bottom:100px;'>
				<div class='row'>
					<div class='col-md-8 col-md-offset-2'>
						<h2 class="text-center">Moderators</h2>
						<table class='table table-striped'>
							<thead>
							<tr><th>User</th></tr>
							</thead>
							<tbody>
								<?php
								while ($r=mysqli_fetch_array($get_moderators)) {
									echo "<tr><td><a href='http://steamcommunity.com/profiles/".$r["steam_id"]."' target='_blank'><img src='https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/".$r["steam_avatar"].".jpg'>".$r["steam_persona"]."</a></td></tr>";
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