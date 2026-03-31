const rounded = true;
const showClass = "lightSpeedIn";
const hideCalss = "lightSpeedOut";
const pathSound = "/rukada/plugins/notifications/sound/";
const formatSound = ".ogg";


export default  {
    notif_success: function(msg){
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
    },
    notif_info: function(msg){
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
    },
    notif_error: function(msg){
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
    },
}
