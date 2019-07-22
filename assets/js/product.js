require('./jquery.collection');
$(function () {
    $('.variant-collection').collection({
        allow_up: false,
        allow_down: false,
        allow_add: true,
        allow_remove: true,
        allow_duplicate: false,
        up: false,
        down: false,
        add: '<a href="#" class="btn btn-success"><span class="fas fa-plus"></a>',
        remove: '<a href="#" class="btn btn-danger"><span class="fas fa-trash-alt"></a>',
        min: 1,
        add_at_the_end: true,
        init_with_n_elements: 1,
        drag_drop: false,
        fade_in: true,
        fade_out: true,
    });
});
