// Prevengo error al detectar versión de bootstrap con selectpicker.
$.fn.selectpicker.Constructor.BootstrapVersion = bootstrap_version;

// Caja con la fila completa para agregar una red social
var clean_user_social_add = $('.user_social_box').first().clone(true);

/**
 * Añade una fila para introducir una nueva red social del usuario.
 */
function red_social_add() {
    var box = $('#user_social_content_box').prepend(clean_user_social_add.clone(true));
    box.find('.user_social_box_delete').click(red_social_delete);
    $('select').selectpicker();
}

/**
 * Elimina una fila de red social.
 */
function red_social_delete() {
    $(this).closest('.user_social_box').remove();
}

/**
 * Avanza un paso en el formulario para crear usuarios.
 */
function nextStep() {
    // id → user-form-create-tabs
    // Tiene clase "active" el elemento marcado, pillar el hermano y hacer click
    var box = $('#user-form-create-tabs');
    var tabActive = box.find('.active');
    var sibling = tabActive.closest('li').next().find('.nav-link').addClass('active');

    //TODO → si sibling no tiene nada el nodo, volver al primero.

    // Elimino el anterior activo
    tabActive.removeClass('active');

    console.log(tabActive);
    console.log(sibling);
}

/**
 * Lleva un paso atrás en el formulario para crear usuarios.
 */
function backStep() {

}

$('document').ready(() => {
    $('#red_social_add').click(red_social_add);
    $('.user_social_box_delete').click(red_social_delete);

    // Moverse por el formulario.
    $('#user-add-step-left').click(backStep);
    $('#user-add-step-right').click(nextStep);
});
