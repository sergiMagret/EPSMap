<?php

require_once("AppInit.php");
$lang_str = null;
$lang_obj = null;
if(isset($_GET['lang'])) $lang_str = $_GET['lang'];
else $lang_str = DEFAULT_LANGUAGE;

if(file_exists("languages/".$lang_str.".json")) $lang_obj = json_decode(file_get_contents("languages/".$lang_str.".json"), true);
else $lang_obj = json_decode(file_get_contents("languages/".DEFAULT_LANGUAGE.".json"), true);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang_obj['title']; ?></title>
    
    <link rel="stylesheet" href="media/bootstrap-v4.6.1/bootstrap.min.css">
    <link rel="stylesheet" href="media/fontawesome-4.7.0/font-awesome.min.css">
    <link rel="stylesheet" href="media/virtualselect-1.0.26/virtual-select.min.css">
    <link rel="stylesheet" href="media/tooltip-1.0.16/tooltip.min.css">
    <link rel="stylesheet" href="media/styles.css">
</head>
<body>
    <header>
        <div class="header-title">
            <h1><?php echo $lang_obj['title']; ?></h1>
            <h2><?php echo $lang_obj['subtitle']; ?></h2>
        </div>
        <div id="language-select">
            <select name="lang" id="lang" title="<?php echo $lang_obj['language_select'] ?>">
                <option value="ca" <?php if($lang_str == "ca") echo "selected"; ?>>CA</option>
                <option value="es" <?php if($lang_str == "es") echo "selected"; ?>>ES</option>
                <option value="en" <?php if($lang_str == "en") echo "selected"; ?>>EN</option>
            </select>
        </div>
    </header>
    <div class="content">
        <div class="main-title">
            <h3><?php echo $lang_obj['select_destination']; ?>:</h3>
        </div>
        <div>
            <div class="search-option-wrapper">
                <div class="search-option">
                    <input type="checkbox" style="margin-right: 5px;" class="" id="people" name="people"/>
                    <label class="toggle" for="people"><?php echo $lang_obj['teachers']; ?></label>
                </div>
                <div class="search-option">
                    <input type="checkbox" style="margin-right: 5px;" class="" id="space" name="space"/>
                    <label class="toggle" for="space"><?php echo $lang_obj['spaces']; ?></label>
                </div>
            </div>
            <div id="vs-searcher"></div>
            <div>
                <div id="path-instructions" class="d-none"></div>
            </div>
        </div>
    </div>
    <div id="dark-background" class="d-none"></div>
    <div id="fullscreen-image" class="d-none"></div>
    <script src="media/jquery-3.6.0/jquery-3.6.0.min.js"></script>
    <script src="media/bootstrap-v4.6.1/bootstrap.bundle.min.js"></script>
    <script src="media/virtualselect-1.0.26/virtual-select.min.js"></script>
    <script src="media/tooltip-1.0.16/tooltip.min.js"></script>
    <!-- <script src="media/scripts.js"></script> -->
    <script>
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
                searchPathTo(final_node_id).then(path => config.onSelectDestination(final_node_id, path.instructions, path.total_cost, path.destination_zone, path.initial_turn)).catch(jqXHR => console.error(jqXHR));
            });
        }



        const searchParams = (new URL(window.location.href)).searchParams;
        const capture_point_id = parseInt(searchParams.get("cp_id")) || 1;
        const language = searchParams.get("lang") || "es";
        const lang_obj = <?php echo json_encode($lang_obj) ?> || {};
        const active_lang = <?php echo json_encode($_GET['lang'] ?? DEFAULT_LANGUAGE); ?>;


        VirtualSelect_Searcher({
            main: $("#vs-searcher"),
            people: {
                checkbox: $("input[type=checkbox][name=people]")
            },
            spaces: {
                checkbox: $("input[type=checkbox][name=space]")
            },
            capture_point_id: capture_point_id,
            language: "es",
            onSelectDestination: (destination, path, total_cost, destination_zone, initial_turn=null) => {
                const initialTurnText = {
                    'F': lang_obj.initial_turn_forward,
                    'B': lang_obj.initial_turn_backward,
                    'R': lang_obj.initial_turn_right,
                    'L': lang_obj.initial_turn_left,
                };

                let path_instructions = $("#path-instructions");
                path_instructions.removeClass("d-none");
                path_instructions.empty();
                if(initial_turn){
                    path_instructions.append($("<div/>", {
                            class: "initial-turn-instruction"
                        }).append(
                            $("<div/>", { class: "instruction-text" }).text(lang_obj.initially + " " + initialTurnText[initial_turn].toLowerCase() + " " + lang_obj.and_then + ":"),
                            $(`<a tabindex="0" role="button" data-toggle="popover" class="btn-inter-results" style="color: inherit;"><i class="fa fa-question-circle-o" aria-hidden="true" title="${lang_obj.interpret_results_title}"></i></a>`)
                        )
                    );

                    $('.btn-inter-results').popover({
                        trigger: 'focus',
                        content: lang_obj.interpret_results_content,
                        placement: "left",
                        html: true
                    })
                }

                let append_move_forward_text = true;
                const instructions_not_move_forward_text = ["go_1_floor_upstairs", "go_1_floor_downstairs"]; // List of instructions whose following instruction won't include the text to move forward X meters

                path.forEach(edge => {
                    const from_edge = edge.from;
                    const to_edge = edge.to;
                    const instruction_translation = edge.instruction_translation;
                    const has_image = edge.has_image;
                    const instruction_image = has_image ? $("<div/>", {
                        class: "instruction-image"
                    }).append($("<img/>", {
                        src: "actions/getInstructionImage.php?initial_edge_id="+from_edge.id+"&destination_edge_id="+to_edge.id
                    })) : null;

                    const full_instruction_text = append_move_forward_text ? 
                                                lang_obj.move_forward + " " + from_edge.weight + " " + lang_obj.meters + ". " + instruction_translation.text :
                                                instruction_translation.text
                    const instruction_text = $("<div/>", {
                        class: "instruction-text"
                    }).text(full_instruction_text);

                    // Update the variable for the next instruction
                    // Since the move forward is done before the instruction we want to not append the move forward in the next instruction
                    append_move_forward_text = !instructions_not_move_forward_text.includes(instruction_translation.instruction.name);

                    
                    const instruction_wrapper = (
                        $("<div/>", {
                            class: "instruction-wrapper" + (has_image ? " has-image" : "")
                        }).append(instruction_image, instruction_text)
                    );
                    
                    path_instructions.append(instruction_wrapper);
                });
                
                const move_forward_text = $("<div/>", {
                    class: "final-instruction-text"
                }).text(
                    `${lang_obj.move_forward} ${path[path.length -1].to.weight} ${lang_obj.meters} ${lang_obj.and} ${lang_obj.you_will_have_reached_your_destination.toLowerCase()} (${lang_obj.destination_zone} "${destination_zone.name}")`
                );

                path_instructions.append(move_forward_text);
                
            }
        }, active_lang, lang_obj);

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
    </script>
</body>
</html>