<?php

require_once("AppInit.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSMap</title>
    
    <link rel="stylesheet" href="media/bootstrap-v4.6.1/bootstrap.min.css">
    <link rel="stylesheet" href="media/fontawesome-4.7.0/font-awesome.min.css">
    <link rel="stylesheet" href="media/virtualselect-1.0.26/virtual-select.min.css">
    <link rel="stylesheet" href="media/tooltip-1.0.16/tooltip.min.css">
    <link rel="stylesheet" href="media/styles.css">
</head>
<body>
    <div>
        <h1>EPSMap</h1>
        <h2>Sistema de navegació intern pels edificis de la EPS</h2>
    </div>
    <div>
        <h3>Selecciona la teva destinació</h3>
        <div>
            <div class="search-option-wrapper">
                <label class="toggle" for="department">
                    <input type="checkbox" class="toggle__input" id="department" name="department"/>
                    <span class="toggle-track">
                        <span class="toggle-indicator"></span>
                    </span>
                    Department
                </label>
                <label class="toggle" for="destination_zone">
                    <input type="checkbox" class="toggle__input" id="destination_zone" name="destination_zone"/>
                    <span class="toggle-track">
                        <span class="toggle-indicator"></span>
                    </span>
                    Destination Zone
                </label>
                <label class="toggle" for="people">
                    <input type="checkbox" class="toggle__input" id="people" name="people"/>
                    <span class="toggle-track">
                        <span class="toggle-indicator"></span>
                    </span>
                    People
                </label>
                <label class="toggle" for="space">
                    <input type="checkbox" class="toggle__input" id="space" name="space"/>
                    <span class="toggle-track">
                        <span class="toggle-indicator"></span>
                    </span>
                    Space
                </label>
            </div>
            <!-- <div class="searcher-wrapper">
                <div id="searcher"></div>
                <div>
                    <button class="searcher-button btn btn-primary btn-sm" disabled>Search</button>
                </div>
            </div> -->

            <!-- <br><br><br> -->
            <div id="testing"></div>
        </div>
    </div>
    <script src="media/jquery-3.6.0/jquery-3.6.0.min.js"></script>
    <script src="media/bootstrap-v4.6.1/bootstrap.bundle.min.js"></script>
    <script src="media/virtualselect-1.0.26/virtual-select.min.js"></script>
    <script src="media/tooltip-1.0.16/tooltip.min.js"></script>
    <!-- <script src="media/scripts.js"></script> -->
    <script>
        const VirtualSelect_Searcher = function(config){

            if(!config.main) throw new Error("Main node to attach the searcher not given");

            // If the variable starts with "$", that means it holds a jQuery object

            let $button = $("<button/>", { // Button to search
                    "class": "searcher-button btn btn-primary btn-sm",
                    "disabled": 1
                }).text("Search"),
                
                $vs = $("<div/>", { // Div where the vs-comp will be placed
                    "id": randomID(8)
                }),

                $wrapper = $(config.main).addClass("searcher-wrapper").append($vs, $("<div/>").append($button)), // Place everything inside the wrapper, the button is placed inside another div

                vs = VirtualSelect.init({ // Once everything is in the DOM initialize the vs-comp
                    ele: "#"+$vs.attr("id"),
                    search: true,
                    markSearchResults: true,
                    showSelectedOptionsFirst: true,
                    onServerSearch: function(value, virtualSelect){
                        searchValue(value).then(response => updateSelect(response, virtualSelect)).catch(jqXHR => console.error(jqXHR));
                    }
                });


            // Different checkboxes for different search values to include
            let checkbox_departments = (config.departments && config.departments.checkbox) ? $(config.departments.checkbox) : null,
                checkbox_destination_zones = (config.destination_zones && config.destination_zones.checkbox) ? $(config.destination_zones.checkbox) : null,
                checkbox_people = (config.people && config.people.checkbox) ? $(config.people.checkbox) : null,
                checkbox_spaces = (config.spaces && config.spaces.checkbox) ? $(config.spaces.checkbox) : null;
            
            // List with the checkboxes the user gave us
            const active_checkboxes = [];
            checkbox_departments && active_checkboxes.push(checkbox_departments);
            checkbox_destination_zones && active_checkboxes.push(checkbox_destination_zones);
            checkbox_people && active_checkboxes.push(checkbox_people);
            checkbox_spaces && active_checkboxes.push(checkbox_spaces);

            /**
             * Search a value in the database
             */
            async function searchValue(value){
                return new Promise((resolve, reject) => {
                    const include_departments =  (checkbox_departments && checkbox_departments.is(":checked")) ? 1 : 0,
                        include_destination_zones = (checkbox_destination_zones && checkbox_destination_zones.is(":checked")) ? 1 : 0,
                        include_people = (checkbox_people && checkbox_people.is(":checked")) ? 1 : 0,
                        include_spaces = (checkbox_spaces && checkbox_spaces.is(":checked")) ? 1 : 0;
                    
                    if(!include_departments && !include_destination_zones && !include_people && !include_spaces) resolve([]);
    
                    $.ajax({
                        type: "GET",
                        data: {
                            "text_search": value,
                            "limit": 20,
                            "department": include_departments,
                            "destination_zone": include_destination_zones,
                            "people": include_people,
                            "space": include_spaces
                        },
                        url: "actions/search.php",
                        success: response => resolve(response),
                        error: jqXHR => reject(jqXHR)
                    })
                })
            }
    
            /**
             * Update the virtualSelect
             */
            function updateSelect(response){
                let option_groups = [];
                if(checkbox_departments && response.department && response.department.length > 0){
                    let options = {
                        "label": "Departments",
                        "options": []
                    }
                    options.options.push(...response.department.map(elem => {return {"label": elem.name, "value": elem.id}}))
                    option_groups.push(options);
                }
                if(checkbox_destination_zones && response.destination_zone && response.destination_zone.length > 0){
                    let options = {
                        "label": "Destination Zones",
                        "options": []
                    }
                    options.options.push(...response.destination_zone.map(elem => {return {"label": elem.name, "value": elem.id}}))
                    option_groups.push(options);
                }
                if(checkbox_spaces && response.space && response.space.length > 0){
                    let options = {
                        "label": "Spaces",
                        "options": []
                    }
                    options.options.push(...response.space.map(elem => {return {"label": elem.name, "value": elem.id}}))
                    option_groups.push(options);
                }
                
                if(checkbox_people && response.people && response.people.length > 0){
                    let options = {
                        "label": "Spaces",
                        "options": []
                    }
                    options.options.push(...response.people.map(elem => {return {"label": elem.name, "value": elem.id}}))
                    option_groups.push(options);
                }
    
                vs.setServerOptions(option_groups);
            }

            /**
             * Generate a random string
             */
            function randomID(length) {
                let result           = '',
                    letters          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
                    numbers          = '0123456789',
                    characters       = letters+numbers,
                    charactersLength = characters.length;

                result += letters.charAt(Math.floor(Math.random() * letters.length)); // Force the first to be a letter, if the first is a number JS will throw an error

                for ( let i = 1; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }

                return result;
            }
            
            // On change the status of any of the given checkboxes perform an empty search
            active_checkboxes.forEach(elem => {
                elem.on('change', event => { 
                    searchValue("").then(response => updateSelect(response, vs)).catch(jqXHR => console.error(jqXHR));
                });
            })
    
            // When a value is selected enable the button to search
            $vs.on('change', event => {
                if($vs.val() != "") $button.prop("disabled", 0);
                else $button.prop("disabled", 1);
            });
        }






        VirtualSelect_Searcher({
            main: $("#testing"),
            departments: {
                checkbox: $("input[type=checkbox][name=department]")
            },
            destination_zones: {
                checkbox: $("input[type=checkbox][name=destination_zone]")
            },
            people: {
                checkbox: $("input[type=checkbox][name=people]")
            },
            spaces: {
                checkbox: $("input[type=checkbox][name=space]")
            }
        })
    </script>
</body>
</html>