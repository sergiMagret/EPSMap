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
    <script src="media/scripts.js"></script>
    <script>
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
    </script>
</body>
</html>