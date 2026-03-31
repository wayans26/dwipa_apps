<template>
    <header class="topbar-nav">
        <nav class="navbar navbar-expand fixed-top bg-white">
            <ul class="navbar-nav mr-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link toggle-menu" href="javascript:void();">
                        <i class="icon-menu menu-icon"></i>
                    </a>
                </li>
            </ul>
            <span class="title-dinas">Visitor Registration</span>
        </nav>
    </header>
</template>

<script>
export default {
    mounted() {
        $(function () {
            "use strict";

            //sidebar menu js
            $.sidebarMenu($('.sidebar-menu'));

            // === toggle-menu js
            $(document).ready(function () {
                $(".toggle-menu").on("click", function (e) {
                    e.preventDefault();
                    $("#wrapper").toggleClass("toggled");
                });
            });

            // === sidebar menu activation js

            $(function () {
                for (var i = window.location, o = $(".sidebar-menu a").filter(function () {
                    return this.href == i;
                }).addClass("active").parent().addClass("active"); ;) {
                    if (!o.is("li")) break;
                    o = o.parent().addClass("in").parent().addClass("active");
                }
            }),

                /* Back To Top */

                $(document).ready(function () {
                    $(window).on("scroll", function () {
                        if ($(this).scrollTop() > 300) {
                            $('.back-to-top').fadeIn();
                        } else {
                            $('.back-to-top').fadeOut();
                        }
                    });
                    $('.back-to-top').on("click", function () {
                        $("html, body").animate({
                            scrollTop: 0
                        }, 600);
                        return false;
                    });
                });

            $(function () {
                $('[data-toggle="popover"]').popover()
            })


            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })



        });

    }
}
</script>
