// The following line references the definition for the jQuery package, used for autocompletion
/// <reference path="../typings/globals/jquery/index.d.ts"/>

const VirtualSelect_Searcher = function(config, active_lang, lang_obj){
    if(!config.main) throw new Error("Main HTML node to attach the searcher not given");
    if($(config.main).length == 0) throw new Error("The given HTML node is empty");
    if(!config.capture_point_id) throw new Error("Capture point ID for the search was not given");
    if(config.capture_point_id == NaN) throw new Error("Capture point ID must be a number");
    const capture_point_id = config.capture_point_id;
    let search_timeout = null;

    // If the variable starts with "$", that means it holds a jQuery object

    let $button = $("<button/>", { // Button to search
            "class": "searcher-button btn btn-primary btn-sm",
            "disabled": 1,
            "type": "button"
        }).text(lang_obj.search),
        
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
                clearTimeout(search_timeout);
                search_timeout = setTimeout(() => {
                    searchValue(value).then(response => updateSelect(response, virtualSelect)).catch(jqXHR => console.error(jqXHR));
                }, 200);
            }
        });


    // Different checkboxes for different search values to include
    let checkbox_people = (config.people && config.people.checkbox) ? $(config.people.checkbox) : null,
        checkbox_spaces = (config.spaces && config.spaces.checkbox) ? $(config.spaces.checkbox) : null;
    
    // List with the checkboxes the user gave us
    const active_checkboxes = [];
    checkbox_people && active_checkboxes.push(checkbox_people);
    checkbox_spaces && active_checkboxes.push(checkbox_spaces);

    /**
     * Search a value in the database
     * @param {String} value Value to search
     * 
     * @returns {Promise} On resolve returns the list of places found, on reject returns the jqXHR error object
     */
    async function searchValue(value){
        return new Promise((resolve, reject) => {
            const include_people = (checkbox_people && checkbox_people.is(":checked")) ? 1 : 0,
                  include_spaces = (checkbox_spaces && checkbox_spaces.is(":checked")) ? 1 : 0;
            
            if(!include_people && !include_spaces) resolve([]);

            $.ajax({
                type: "GET",
                data: {
                    "text_search": value,
                    "limit": 50,
                    "people": include_people,
                    "space": include_spaces
                },
                url: "actions/search.php",
                success: response => resolve(response),
                error: jqXHR => reject(jqXHR)
            })
        })
    }

    async function searchPathTo(destination_node_id){
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                data: {
                    lang: active_lang,
                    capture_point_id: capture_point_id,
                    destination_node_id: destination_node_id
                },
                url: "actions/get_path.php",
                success: response => resolve(response),
                error: jqXHR => reject(jqXHR)
            })
        });
    }

    /**
     * Get the name to use when displaying a space to the user
     * 
     * @param {Object} space_obj The JSON object space
     * @param {Boolean} in_person Whether the space will be used inside a person's name or not
     * 
     * @returns {String} The value to use
     */
    function getSpaceName(space_obj, in_person=false){
        const space_types = {
            1: lang_obj.space_type_class,
            2: lang_obj.space_type_toilet,
            3: lang_obj.space_type_office,
            4: lang_obj.space_type_lab,
            5: lang_obj.space_type_class_comp,
            6: lang_obj.space_type_greenhouse,
            7: lang_obj.space_type_auditorium,
        };

        if(in_person) return space_obj.name;
        else {
            return space_obj.name + " (" + (space_obj.alias ? space_obj.alias + ", " : "") + space_types[space_obj.space_type_id] + ")";
        }
    }

    /**
     * Get the name to use when displaying a person to the user
     * 
     * @param {Object} person_obj The JSON object person
     * 
     * @returns {String} The value to use
     */
    function getPersonName(person_obj){
        return person_obj.name + " (" + (person_obj.department.alias || person_obj.department.name) + ", " + getSpaceName(person_obj.space, true) + ")";
    }


    /**
     * Update the virtualSelect
     * 
     * @param {Array} new_values An array with the new values returned by the server
     * 
     * @returns {void}
     */
    function updateSelect(new_values){
        // The values for the VirtualSelect have a preappended random value to avoid problems when two differents objects point to the same final node id
        // The values need to be unique, and without the random value, they wouldn't be unique, you can get the actual ID with value.split("-")[1]
        let option_groups = [];
        if(checkbox_spaces && new_values.space && new_values.space.length > 0){
            let options = {
                "label": lang_obj.spaces,
                "options": []
            }
            options.options.push(...new_values.space.map(elem => { 
                return { 
                    "label": getSpaceName(elem), 
                    "value": randomID(3)+"-"+elem.destination_zone.main_node 
                } 
            }))
            option_groups.push(options);
        }
        
        if(checkbox_people && new_values.people && new_values.people.length > 0){
            let options = {
                "label": lang_obj.teachers,
                "options": []
            }
            options.options.push(...new_values.people.map(elem => { 
                return { 
                    "label": getPersonName(elem),
                    "value": randomID(3)+"-"+elem.main_node.id 
                }
            }))
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

    $button.on('click', event => {
        const final_node_id = $vs.val().split("-")[1];
        searchPathTo(final_node_id).then(path => config.onSelectDestination(final_node_id, path)).catch(jqXHR => console.error(jqXHR));
    });
}

/**
 * Attach an event listener to open the images in full screen and close them
 * once the user tap anywhere else than the image
 */
var FullScreenImage = function(){
    const close_image = () => {
        if(event.target != $("#fullscreen-image img")[0]){
            $("body").removeClass("noscroll");
            $("#dark-background").addClass("d-none");
            $("#fullscreen-image").addClass("d-none");
            $(document.body).off('click', close_image);
        }
    };

    $("#path-instructions").on("click", ".instruction-image img", event => {
        $("body").addClass("noscroll");
        $("#dark-background").removeClass("d-none");
        const fullscreen_image = $("#fullscreen-image");
        fullscreen_image.removeClass("d-none");
        fullscreen_image.empty();
        fullscreen_image.append($("<img/>", {
            src: event.currentTarget.src
        }));

        setTimeout(() => {
            $(document.body).on('click', close_image);
        }, 100);
    });
}();

/**
 * Listener for when the user wants to change the language of the application
 */
var ChangeLanguage = function(){
    $("#lang").on('change', event => {
        const params = new URLSearchParams();
        params.set("lang", $(event.currentTarget).val());
        window.location.href = window.location.pathname + "?" + params; // Reload the same page with the new param
    });
}();