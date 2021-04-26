<?php
echo "string";
// Show all information, defaults to INFO_ALL
phpinfo();

// Show just teh module information.
// phpinfo(8) yields identical results.
phpinfo(INFO_MODULES);

?>