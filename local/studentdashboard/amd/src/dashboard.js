/**
 * Student Dashboard JavaScript functionality
 *
 * @module     local_studentdashboard/dashboard
 * @copyright  2024 Learning Dashboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize the dashboard
     */
    function init() {
        $(document).ready(function() {
            initProgressCircles();
            initCardAnimations();
        });
    }

    /**
     * Initialize progress circle animations
     */
    function initProgressCircles() {
        $('.progress-circle').each(function() {
            var $circle = $(this);
            var $progressFill = $circle.find('.progress-fill');
            var progress = parseInt($progressFill.attr('data-progress')) || 0;
            
            // Calculate stroke-dashoffset based on progress
            // Circle circumference = 2 * π * radius = 2 * π * 60 = 377
            var circumference = 377;
            var offset = circumference - (progress / 100) * circumference;
            
            // Animate after a delay
            setTimeout(function() {
                $progressFill.css('stroke-dashoffset', offset);
            }, 500);
        });
    }

    /**
     * Initialize card hover animations
     */
    function initCardAnimations() {
        // Add smooth transitions to course cards
        $('.course-card').on('mouseenter', function() {
            $(this).addClass('card-hover');
        }).on('mouseleave', function() {
            $(this).removeClass('card-hover');
        });

        // Add ripple effect to buttons
        $('.resume-btn, .continue-btn').on('click', function(e) {
            var $button = $(this);
            var $ripple = $('<span class="ripple"></span>');
            
            var buttonOffset = $button.offset();
            var x = e.pageX - buttonOffset.left;
            var y = e.pageY - buttonOffset.top;
            
            $ripple.css({
                left: x + 'px',
                top: y + 'px'
            });
            
            $button.append($ripple);
            
            setTimeout(function() {
                $ripple.remove();
            }, 600);
        });

        // Stagger animation for recent items
        $('.recent-item-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });

        // Stagger animation for course cards
        $('.course-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    }

    return {
        init: init
    };
});