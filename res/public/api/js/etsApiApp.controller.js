angular
    .module("ets-api-app", [],
        function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        })
    .service('getDataService', function ($http) {
        this.getApiJSON = function () {
            return $http({
                method: 'GET',
                url: '/docs/api.json'
            });
        };
    })
    .controller("ets-api-controller", function ($scope, $http, getDataService, $rootScope) {

        /* Get JSON file with all data */
        getDataService.getApiJSON().success(function (response) {
            $scope.apiName = response.apiName;
            $scope.parts = [];

            for (var i = 0; i <= response.parts.length - 1; i++) {
                $scope.parts.push({
                    "name": response.parts[i].name,
                    "data": response.parts[i].data,
                    "active": false
                });
            }

            /* Default settings */
            $scope.parts[0].active = true;
            $scope.list = $scope.parts[0].data;
            $scope.currentPart = $scope.parts[0].name;
        });

        /* Main menu logic */
        $scope.changePage = function ($event) {
            var elClass = $event.target.classList;

            for (var i = 0; i <= $scope.parts.length - 1; i++) {
                if (elClass.contains($scope.parts[i].name)) {
                    $scope.parts[i].active = true;
                    $scope.list = $scope.parts[i].data;
                } else {
                    $scope.parts[i].active = false;
                }
            }

            document.getElementsByClassName('hamburger')[0].parentElement.children[1].classList.remove('displayBlock');
            $scope.currentPart = $event.target.innerHTML;
        };

        var openedDesc = [];

        $scope.showDescription = function (event) {
            /* Variables */
            var apiDescription = event.currentTarget.children[2],
                apiName = event.currentTarget.children[0],
                codeBlock = apiDescription.getElementsByClassName('code-block')[0],
                showMoreBtn = apiDescription.getElementsByClassName('show-more-btn')[0],
                parsedJSON,
                parsed,
                objKeys,
                formatedCode;

            /* If description is open - stop doing things. */
            if (!event.target.classList.contains('api-headline') && !event.target.parentElement.classList.contains('api-headline')) {

                return false;
            }

            /* Close other descriptions before open new.
             *  Also remove white bg for headline - because hover
             *  and remove green color of api name. */
            if (openedDesc.length !== 0) {
                for (var i = 0; i < openedDesc.length; i++) {
                    if (openedDesc[i] !== apiDescription) {
                        openedDesc[i].classList.remove("opened");
                        openedDesc[i].parentElement.children[0].style.color = '#372b43';
                        openedDesc[i].parentElement.style.background = null;
                    }
                }

                openedDesc = [];
            }

            /*
             * 1. Open and close api descriptions
             * 2. Change api name h3 tag to green when active
             * 3. Change headline bg to white - stop hover
             * 4. Change src of arrow img (down - up)*/
            if (!apiDescription.classList.contains("opened")) {
                apiDescription.classList.add("opened");
                openedDesc.push(apiDescription);
                apiName.style.color = '#3cd51d';
                event.currentTarget.style.background = '#ffffff';
                $scope.showCodeState = 'down-arrow.png';

                /* Color code example */
                try {
                    objKeys = getObjKeys(JSON.parse(codeBlock.innerHTML));
                    parsed = true;
                } catch (e) {
                    parsed = false;
                }

                if (parsed) {
                    formatedCode = codeBlock.innerHTML.replace(/,/g, "<span class='punctuation'>,</span>");
                    formatedCode = formatedCode.replace(/:/g, "<span class='punctuation'>:</span>");
                    formatedCode = formatedCode.replace(/{/g, "<span class='punctuation'>{</span>");
                    formatedCode = formatedCode.replace(/}/g, "<span class='punctuation'>}</span>");
                    formatedCode = formatedCode.replace(/\[/g, "<span class='punctuation'>[</span>");
                    formatedCode = formatedCode.replace(/]/g, "<span class='punctuation'>]</span>");

                    for (var i = 0; i < objKeys.length; i++) {
                        if (+objKeys[i]) {
                            continue;
                        }
                        ;

                        formatedCode = formatedCode.replace(new RegExp('"' + objKeys[i] + '"', "g"), "<span class='boolean_number_string'>" + '"' + objKeys[i] + '"' + "</span>");
                    }

                    codeBlock.innerHTML = formatedCode;
                }

            } else {
                apiDescription.classList.remove("opened");
                apiName.style.color = '#372b43';
                event.currentTarget.removeAttribute("style");
                $scope.showCodeState = 'down-arrow.png';
            }

            /* Check if need show-more btn for code */
            if (codeBlock.offsetHeight <= 130) {
                showMoreBtn.classList.add('displayNone');
            }
        };

        /* Close and open more code and change arrows */
        $scope.clickShowMoreCode = function (event) {
            var ch = event.currentTarget.parentElement.children;

            if (ch[0].classList.contains('autoHeight')) {
                $scope.showCodeState = 'down-arrow.png';
                ch[0].classList.remove('autoHeight');
            } else {
                $scope.showCodeState = 'up-arrow.png';
                ch[0].classList.add('autoHeight');
            }
        };

        function getObjKeys(obj) {
            parseObj(obj);

            return keysArr;
        }

        var keysArr = [];

        function parseObj(obj) {
            for (var key in obj) {
                keysArr.push(key);

                if (typeof obj[key] === "object") {
                    parseObj(obj[key]);
                } else {
                    keysArr.push(key);
                }
            }
        }

        /* Open/close hamburger mobile menu */
        $scope.openMobileMenu = function (event) {
            var menuBtn = document.getElementsByClassName('hamburger')[0];

            menuBtn.parentElement.children[1].classList.toggle('displayBlock');
        };


    })
    .filter("formatToCode", function () {
        return function (json) {
            return JSON.stringify(json, undefined, 2);
        };
    })
    .directive('qtip', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.qtip(
                    {
                        content: {
                            text: function (event, api) {
                                return $(this).find(".qtip_hide").html();
                            },
                            title: function (event, api) {
                                return "Params";
                            }
                        },
                        hide: {
                            fixed: true,
                            delay: 100
                        },
                        style: {
                            classes: 'qtip-default  qtip-pos-tl'
                        },
                        position: {
                            my: 'right center',
                            at: "left center"
                        }
                    }
                );
            }
        }
    });
