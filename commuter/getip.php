<?php

// Get the value of the "role" session variable
$role = $_SESSION["role"];

// Echo the value of "role"
echo $role;
?>
<?php
function getPublicIP()
{
    $url = 'https://httpbin.org/ip';
    $response = file_get_contents($url);

    if ($response) {
        $data = json_decode($response, true);
        return $data['origin'];
    } else {
        return 'Unable to retrieve public IP address.';
    }
}

// Example usage:
$public_ip = getPublicIP();
echo "Your public IP address is: $public_ip";
?>