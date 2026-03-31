var rounded = true;
var showClass = "lightSpeedIn";
var hideCalss = "lightSpeedOut";
var pathSound = "/rukada/plugins/notifications/sound/";
var formatSound = ".ogg";

function notif_default(title, msg) {
    Lobibox.notify('default', {
        pauseDelayOnHover: true,
        rounded: rounded,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
        title: title,
        showClass: showClass,
        hideClass: hideCalss
    });
}

function notif_info(msg) {
    Lobibox.notify('info', {
        pauseDelayOnHover: true,
        rounded: rounded,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-info-circle',
        msg: msg,
        showClass: showClass,
        hideClass: hideCalss,
        soundPath: pathSound,
        soundExt: formatSound
    });
}

function notif_warning(msg) {
    Lobibox.notify('warning', {
        pauseDelayOnHover: true,
        rounded: rounded,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-exclamation-circle',
        msg: msg,
        showClass: showClass,
        hideClass: hideCalss,
        soundPath: pathSound,
        soundExt: formatSound
    });
}

function notif_error(msg) {
    Lobibox.notify('error', {
        pauseDelayOnHover: true,
        rounded: rounded,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-times-circle',
        msg: msg,
        showClass: showClass,
        hideClass: hideCalss,
        soundPath: pathSound,
        soundExt: formatSound
    });
}

function notif_success(msg) {
    Lobibox.notify('success', {
        pauseDelayOnHover: true,
        rounded: rounded,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-check-circle',
        msg: msg,
        showClass: showClass,
        hideClass: hideCalss,
        soundPath: pathSound,
        soundExt: formatSound
    });
}
