<?php
$access_token = "access_token=CAAGllkm2QUkBAIET6akEGByJ0iVvoqZBhCy5tTTvRemEeyRayX4nYjESGTMG6Xf0W7oyaENBFTpsh4ZCz0zwUy4K3M2PENTkGbiF5JcyPUuHRfoQgAJqk4
NmZCtprGoLxzVog1mK9lH1FZBhYT3LnNOAK84pU04ZD&expires=5103842";
echo $access_token = substr($access_token, strpos($access_token, "=") + 1, strlen($access_token));
?>