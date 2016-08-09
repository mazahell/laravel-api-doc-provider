<?php
/**
 * Simple config for DocProvider
 */
return [
    /**
     * Api Name on main page
     */
    'api_name'                      => 'Your API_NAME',

    /**
     * rd => /rd/docs
     * my_doc => /my_doc/docs
     * secret_url_doc => /secret_url_doc/docs
     */
    'route_prefix'                  => '',

    /**
     * Usage Default routes
     */
    'usage_default_routes'          => true,
    /**
     * Use postman exportFile
     */
    'use_postman_collection_export' => false,

    /**
     * Use angular exportFile
     */
    'use_angular_routes_export'     => false,

];

