/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";

// You can specify which plugins you need
import { Tooltip, Toast, Popover } from "bootstrap";

// start the Stimulus application
import "./bootstrap";

const $ = require("jquery");
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
//require('bootstrap');

window.bootstrap = require("bootstrap");

var tooltipTriggerList = [].slice.call(document.querySelectorAll("[data-bs-toggle='tooltip']"));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

$(function () {
    var page = 1;

    $("#showMoreTricks").on("click", function () {

        var index = $("#showMoreTricks").data("index");

        var nbTrickByPage = $(this).data("tricksbypage");
        var nbtrick = $(this).data("nbtrick");
        var newNbTrick = page * nbTrickByPage;
        var urlDest = "/showmoretrick/" + newNbTrick;
        // AJAX request
        $.ajax({
            url: urlDest,
            type: "post",
            data: {},
            success: function (response) {
                $("#listTrick").append(response);
                if ((newNbTrick + nbTrickByPage) < nbtrick) {
                    page++;
                } else {
                    $("#trickplus").hide();
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    let items = document.querySelectorAll(".carousel .carousel-item");

    items.forEach((el) => {
        const minPerSlide = 4;
        let next = el.nextElementSibling;
        for (var i = 1; i < minPerSlide; i++) {
            if (!next) {
                // wrap carousel by using first child
                next = items[0];
            }
            let cloneChild = next.cloneNode(true);
            el.appendChild(cloneChild.children[0]);
            next = next.nextElementSibling;
        }
    });

    var pageComment = 1;

    $("#showMoreComments").on("click", function () {

        var index = $("#showMoreComments").data("index");

        var trickId = $(this).data("trick");
        var nbCommentByPage = $(this).data("commentsbypage");
        var nbcomment = $(this).data("nbcomments");
        var newNbComment = pageComment * nbCommentByPage;
        var urlDest = "/morecomment/" + newNbComment;
        // AJAX request
        $.ajax({
            url: urlDest,
            type: "post",
            data: { trickId: trickId },
            success: function (response) {
                $("#listComment").append(response);
                if ((newNbComment + nbCommentByPage) < nbcomment) {
                    pageComment++;
                } else {
                    $("#commentplus").hide();
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    $("#hideMedias").hide();

    $("#viewMedias").on("click", function () {
        $("#largeView").show();
        $("#viewMedias").hide();
        $("#hideMedias").show();
    });

    $("#hideMedias").on("click", function () {
        $("#largeView").hide();
        $("#viewMedias").show();
        $("#hideMedias").hide();
    });
})
