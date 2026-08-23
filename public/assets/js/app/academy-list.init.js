/*
Template Name: Takha CRM - Admin & Dashboard Template
Author: Pene Technologies Service (PTS)
Website: https://pixeleyez.com/
Contact: pixeleyez@gmail.com
File: Academy list init js
*/

document.addEventListener('DOMContentLoaded', function () {
    let status = document.getElementById('status');
    if (status) {
        const choicesstatus = new Choices('#status', {
            placeholderValue: 'Select Status',
            searchPlaceholderValue: 'Search...',
            removeItemButton: true,
            itemSelectText: 'Press to select',
        });
    }
});