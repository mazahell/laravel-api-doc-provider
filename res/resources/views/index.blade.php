<!DOCTYPE html>
<html lang="en" ng-app="ets-api-app">
<head>
    <meta charset="UTF-8">
    <title>{{ config("restio_doc.api_name", "Your APINAME") }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link href="{{ url('restio_api/css/style.min.css') }}" rel="stylesheet"/>

    {{--qTIP--}}
    <link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">
    {{--qTIP--}}
    <style>
        .table_params td:first-child {
            padding-right: 5px;
            padding-top: 5px;
            padding-bottom: 5px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .table_params td:last-child {
            padding-right: 5px;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .table_params td {
            font-size: 1.1em;
        }
    </style>
    <script>
        var api_url = '{{ route('docs_json') }}';
    </script>
</head>
<body ng-controller="ets-api-controller">

<header>
    <a href="{{ route('generate_docs') }}">
        <h1><% apiName %></h1>
    </a>
    @if(config("restio_doc.use_postman_collection_export", false))
        <a href="{{ route('docs_postman') }}" style="top: 120px; font-size: 0.4em;">
            <h1><i class="fa fa-download"></i> postman.json</h1>
        </a>
    @endif

    @if(config("restio_doc.use_angular_routes_export", false))
        <a href="{{ route('restio_angular_url') }}" style="top: 150px; font-size: 0.4em;">
            <h1><i class="fa fa-download"></i> AngularURLs</h1>
        </a>
    @endif

    <ul class="api-list" ng-class="{ 'smallFont': parts.length > 10 }">
        <li ng-repeat="part in parts"
            ng-click="changePage($event)"
            class="<% part.name %>"
            ng-class="{ 'nav-active': part.active }"><% part.name %>
        </li>
    </ul>

    <div class="hamburger" ng-click="openMobileMenu($event)"></div>
</header>

<div class="mobilePartHeader">
    <h4><% currentPart %></h4>
</div>


<section>
    <div class="api-headline" ng-repeat="item in list" ng-click="showDescription($event)">
        <h3><% item.name %></h3>
        <p><% item.description %><span><% item.method %></span></p>
        <div class="api-description">
            <table ng-hide="item.params.length === 0">
                <thead>
                <tr>
                    <th>Parameters</th>
                    <th>Descriptions</th>
                </tr>
                </thead>
                <tbody>
                <tr qtip ng-repeat="(key, param) in item.required_params track by $index">
                    <td><% key %><span style="color:red">*</span></td>
                    <td>
                        <b><% param.description %></b> (type: <% param.type %>)
                        <div class="qtip_hide hidden" style="display: none">
                            <table class="table_params">
                                <tr class="params_tr" ng-repeat="(key, rp) in param track by $index">
                                    <td ng-bind="key + ':'"></td>
                                    <td ng-bind="rp"></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr qtip ng-repeat="(key, param) in item.optional_params">
                    <td><% key %></td>
                    <td>
                        <b><% param.description %></b> (type: <% param.type %>)
                        <div class="qtip_hide" style="display: none">
                            <table class="table_params">
                                <tr class="params_tr" ng-repeat="(key, rp) in param track by $index">
                                    <td ng-bind="key + ':'"></td>
                                    <td ng-bind="rp"></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <h4>Success response (example)</h4>
            <div class="code-example">
                        <pre>
                            <code class="code-block">
<% item.response | formatToCode %>
                            </code>
                        </pre>
                <div class="show-more-btn" ng-click="clickShowMoreCode($event)">
                    <img ng-src="{{ url('restio_api/img') }}/<% showCodeState %>" alt="down arrow">
                </div>
            </div>
            <div class="mini-footer"></div>
        </div>
    </div>
</section>

{{--qTIP--}}
<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
<script type="text/javascript" src="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>
<script type="text/javascript" src="http://imagesloaded.desandro.com/imagesloaded.pkgd.min.js"></script>
<script src="https://use.fontawesome.com/0a064f956f.js"></script>

<script src="{{ url('restio_api/plugins/angular.min.js') }}" type="text/javascript"></script>
<script src="{{ url('restio_api/js/etsApiApp.module.js') }}" type="text/javascript"></script>
<script src="{{ url('restio_api/js/etsApiApp.controller.js') }}" type="text/javascript"></script>


</body>
</html>
