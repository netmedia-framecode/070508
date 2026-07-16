<?php
$midtrans_is_production = false;
$midtrans_server_key = getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-IyDnIUPJsi6onjoa1gCVgYSJ';
$midtrans_client_key = getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-WQkDTLiXwuA-YILv';

$midtrans_snap_api_url = $midtrans_is_production
  ? 'https://app.midtrans.com/snap/v1/transactions'
  : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

$midtrans_status_api_base_url = $midtrans_is_production
  ? 'https://api.midtrans.com/v2/'
  : 'https://api.sandbox.midtrans.com/v2/';

$midtrans_snap_js_url = $midtrans_is_production
  ? 'https://app.midtrans.com/snap/snap.js'
  : 'https://app.sandbox.midtrans.com/snap/snap.js';
