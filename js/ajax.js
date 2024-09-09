function getOptions() {
    var object;
    $.ajax({
        url: '../plugins/splitdropdown/ajax/getOptions.php',
        type: "GET",
        async: false,
        success: function (response) {
            object = JSON.parse(response);
        }
    });
    return object;
}

async function addDropdowns(options) {
    for (var element in options) {
        var elements = [];
        elements.push(element);
        await $.ajax({
            url: '../plugins/splitdropdown/ajax/parent.dropdown.php',
            type: "GET",
            data: { split: `${options[element]["options"]["split"]}`, level: `${options[element]["options"]["level"]}`, table: `${options[element]["values"]["table"]}`, itemType_id: `${options[element]["values"]["itemType_id"]}`, itemType: `${options[element]["values"]["itemType"]}` },
            success: function (parents) {
                if (options[element]["options"]["split"] === 1) {
                    var location = $(`select[name^=${options[element]["values"]["itemType_id"]}]`).parent();
                    location.empty();
                    location.html(parents + `<span id='children_${options[element]["values"]["itemType_id"]}'></span>`);
                    $("form").on('submit', function () {
                        $(`select[id^=dropdown_parent_dropdown_${options[element]["values"]["itemType_id"]}]`).remove();
                    });
                }
            }
        });
        elements.forEach(element => {
            $(`select[id^=dropdown_parent_${options[element]["values"]["itemType_id"]}]`).on('change', async function () {
                var fk = $(`select[id^=dropdown_parent_${options[element]["values"]["itemType_id"]}]`).val();
                await $.ajax({
                    url: '../plugins/splitdropdown/ajax/children.dropdown.php',
                    type: "GET",
                    data: { fk: fk, table: `${options[element]["values"]["table"]}`, itemType: `${options[element]["values"]["itemType"]}`, itemType_id: `${options[element]["values"]["itemType_id"]}` },
                    success: function (children) {
                        if ($(`select[id^=dropdown_${options[element]["values"]["itemType_id"]}]`).length <= 0) {
                            $(`#children_${options[element]["values"]["itemType_id"]}`).append(children);
                        }
                        else {
                            $(`#children_${options[element]["values"]["itemType_id"]}`).empty();
                            $(`#children_${options[element]["values"]["itemType_id"]}`).append(children);
                        }
                    }
                });
            });
        });
    }
}

const url = window.location.pathname;

if (url.search(/plugin/) < 0) {
    var options = getOptions();
    var i = 0;

    if (options["inNewTicket"] === true) {
        delete options["inNewTicket"];
        $(document).ajaxStop(function () {
            if (i < 1) {
                addDropdowns(options);
                i++;
            }
        });
    }
}