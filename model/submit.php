<?php

$post = $_POST;

if ( isset($post['tradersoft_submit']))
{
    $action = $post['tradersoft_submit'];
    switch ($action)
    {
        case 'authorization':
            require_once dirname(__FILE__) . '/authorization.php'; // TODO : Delete this
            break;
        case 'contact_us':
            require_once dirname(__FILE__) . '/contact_us.php';
            break;        
        case 'call_back':
            require_once dirname(__FILE__) . '/call_back.php';
            break;
        default:
            break;
    }
}
else 
{
    /* 
     * Include JS validation
     */
    add_action( 'wp_loaded', function() {
        require_once dirname(__FILE__) . '/js.php';
    });
}


?>
