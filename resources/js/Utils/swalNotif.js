import Swal from "sweetalert2";

export default {
    success: (text) => {
        Swal.fire({
            icon: 'success',
            title: "Success",
            text: text
        })
    },
    error: (text) => {
        Swal.fire({
            icon: 'error',
            title: "Error",
            text: text
        })
    },
    warning: (text) => {
        Swal.fire({
            icon: 'warning',
            title: "Warning",
            text: text
        })
    },
    info: (text) => {
        Swal.fire({
            icon: 'info',
            title: "Info",
            text: text
        })
    }
}
