
var {registerBlockType} = window.wp.blocks;
var CreateElement = window.wp.element.createElement;
var Fragment = window.wp.element.Fragment;
var {RichText, MediaUpload} = window.wp.editor;
var {InspectorControls, withColors, PanelColorSettings, getColorClassName} = window.wp.blockEditor;
var {Notice, TextControl, ToggleControl, Button, SelectControl, PanelBody, PanelRow, RangeControl, RadioControl} = window.wp.components;
var __ = window.wp.i18n.__;
var sfcp_available_modules = my_ajax_object.bcp_all_modules;
var sfcp_available_modules_tmp = [{label: 'All', value: 'All'}];
//var sfcp_available_modules_tmp = [{label: 'Select Module', value: ''}, {label: 'All', value: 'All'}];
var sfcp_available_modules_all = sfcp_available_modules_tmp.concat(sfcp_available_modules)
var image_prefix = my_ajax_object.image_prefix;
var DefaultDisplayStyle = 'style_1';
var bcp_page_types = my_ajax_object.bcp_page_types;
var sfcp_available_modules_chart = Object.assign({}, sfcp_available_modules);
sfcp_available_modules_chart = removeAttchmentModule(sfcp_available_modules_chart, 'label', 'Attachment');
var registerPlugin = wp.plugins.registerPlugin;
var {PluginDocumentSettingPanel, PluginPrePublishPanel} = wp.editPost;
var useSelect = wp.data.useSelect;
var useEntityProp = wp.coreData.useEntityProp;
const {select, subscribe} = wp.data;
const locks = [];

function lockPageSave(lockIt, handle, message) {
    if (lockIt) {
        if (!locks[ handle ]) {
            locks[ handle ] = true;
            wp.data.dispatch('core/editor').lockPostSaving(handle);
            wp.data.dispatch('core/notices').createNotice(
                    'error',
                    message,
                    {id: handle, isDismissible: false}
            );
        }
    } else if (locks[ handle ]) {
        locks[ handle ] = false;
        wp.data.dispatch('core/editor').unlockPostSaving(handle);
        wp.data.dispatch('core/notices').removeNotice(handle);
    }
}

function removeAttchmentModule(data, key, value) {
    if (value == undefined) {
        return;
    }
    for (var i in data) {

        if (data[i][key] == value) {
            delete data[i];
        }
    }
    return Object.values(data)
}


const {addFilter} = wp.hooks;
const {Component} = wp.element;
const colorSamples = [
    {name: 'Azure Radiance', slug: 'azure_radiance', color: '#00A8FF'},
    {name: 'Lavender', slug: 'lavender', color: '#E6E6FA'},
    {name: 'Red Violet', slug: 'red_Violet', color: '#DB1A6E'},
    {name: 'Green Haze', slug: 'green_haze', color: '#00A63B'},
    {name: 'Dodger Blue', slug: 'dodger_Blue', color: '#4C65FF'}
];
const DefaultBlockColors = [
    {'style_1': {
            'background_color': '#ffffff',
            'background_hover_color': '#E5E9FF',
            'font_color': '#4C65FF',
            'font_hover_color': '#4C65FF',
        }},
    {'style_2': {
            'background_color': '#9865db',
            'background_hover_color': '#9865db',
            'font_color': '#ffffff',
            'font_hover_color': '#ffffff'
        }},
    {'style_3': {
            'background_color': '#ffffff',
            'background_hover_color': '#415cfa',
            'font_color': '#000000',
            'font_hover_color': '#ffffff',
        }},
    {'style_4': {
            'background_color': '#ffffff',
            'background_hover_color': '#6A6EDF',
            'font_color': '#6A6EDF',
            'font_hover_color': '#ffffff',
        }},
    {'style_5': {
            'background_color': '#664BE0',
            'background_hover_color': '#ffffff',
            'font_color': '#ffffff',
            'font_hover_color': '#664BE0',
        }},
    {'style_6': {
            'background_color': '#B127F4',
            'background_hover_color': '#B127F4',
            'font_color': '#ffffff',
            'font_hover_color': '#ffffff',
        }},
];
const RegistrationImages = {
    'style_1': image_prefix + '/signup/signup_style_1.png',
    'style_2': image_prefix + '/signup/signup_style_2.png',
    'style_3': image_prefix + '/signup/signup_style_3.png',
};
const ListingImages = {
    'style_1': image_prefix + '/list/list_style_1.png',
    'style_2': image_prefix + '/list/list_style_2.png',
    'style_3': image_prefix + '/list/list_style_3.png',
};
const DetailImages = {
    'style_1': image_prefix + '/detail/detail_style_1.png',
    'style_2': image_prefix + '/detail/detail_style_2.png',
};
const AddImages = {
    'style_1': image_prefix + '/add/add_style_1.png',
    'style_2': image_prefix + '/add/add_style_2.png',
    'style_3': image_prefix + '/add/add_style_3.png',
};
const ProfileImages = {
    'style_1': image_prefix + '/profile/profile_style_1.png',
    'style_2': image_prefix + '/profile/profile_style_2.png',
    'style_3': image_prefix + '/profile/profile_style_3.png',
};
const ActivityImages = {
    'style_1': image_prefix + '/recent_activity/recent_activity_style_1.png',
    'style_2': image_prefix + '/recent_activity/recent_activity_style_2.png',
    'style_3': image_prefix + '/recent_activity/recent_activity_style_3.png',
    'style_4': image_prefix + '/recent_activity/recent_activity_style_4.png',
    'style_5': image_prefix + '/recent_activity/recent_activity_style_5.png',
};
const KBImages = {
    'style_1': image_prefix + '/knowledgebase/style-1.png',
    'style_2': image_prefix + '/knowledgebase/style-2.png',
}
const CaseDeflectionImages = {
    'style_1': image_prefix + '/case-deflection/style-1.png',
    'style_2': image_prefix + '/case-deflection/style-2.png',

}

const ChartImages = {
    "chart_images": {
        'style_1': image_prefix + '/chart/chart_style_1.png',
        'style_2': image_prefix + '/chart/chart_style_2.png',
        'style_3': image_prefix + '/chart/chart_style_3.png',
        'style_4': image_prefix + '/chart/chart_style_4.png',
    },
    "chart_images_with_legend": {
        'style_1': image_prefix + '/chart/chart_style_1-legend.png',
        'style_2': image_prefix + '/chart/chart_style_2-legend.png',
        'style_3': image_prefix + '/chart/chart_style_3-legend.png',
        'style_4': image_prefix + '/chart/chart_style_4-legend.png',
    }
};

var fnt_icons_categorized = ["lnr lnr-home", "lnr lnr-apartment", "lnr lnr-pencil", "lnr lnr-magic-wand", "lnr lnr-drop", "lnr lnr-lighter", "lnr lnr-poop", "lnr lnr-sun", "lnr lnr-moon", "lnr lnr-cloud", "lnr lnr-cloud-upload", "lnr lnr-cloud-download", "lnr lnr-cloud-sync", "lnr lnr-cloud-check", "lnr lnr-database", "lnr lnr-lock", "lnr lnr-cog", "lnr lnr-trash", "lnr lnr-dice", "lnr lnr-heart", "lnr lnr-star", "lnr lnr-star-half", "lnr lnr-star-empty", "lnr lnr-flag", "lnr lnr-envelope", "lnr lnr-paperclip", "lnr lnr-inbox", "lnr lnr-eye", "lnr lnr-printer", "lnr lnr-file-empty", "lnr lnr-file-add", "lnr lnr-enter", "lnr lnr-exit", "lnr lnr-graduation-hat", "lnr lnr-license", "lnr lnr-music-note", "lnr lnr-film-play", "lnr lnr-camera-video", "lnr lnr-camera", "lnr lnr-picture", "lnr lnr-book", "lnr lnr-bookmark", "lnr lnr-user", "lnr lnr-users", "lnr lnr-shirt", "lnr lnr-shirt", "lnr lnr-cart", "lnr lnr-tag", "lnr lnr-phone-handset", "lnr lnr-phone", "lnr lnr-pushpin", "lnr lnr-map-marker", "lnr lnr-map", "lnr lnr-location", "lnr lnr-calendar-full", "lnr lnr-keyboard", "lnr lnr-spell-check", "lnr lnr-screen", "lnr lnr-smartphone", "lnr lnr-tablet", "lnr lnr-laptop", "lnr lnr-laptop-phone", "lnr lnr-power-switch", "lnr lnr-bubble", "lnr lnr-heart-pulse", "lnr lnr-construction", "lnr lnr-pie-chart", "lnr lnr-chart-bars", "lnr lnr-gift", "lnr lnr-diamond", "lnr lnr-linearicons", "lnr lnr-dinner", "lnr lnr-coffee-cup", "lnr lnr-leaf", "lnr lnr-paw", "lnr lnr-rocket", "lnr lnr-briefcase", "lnr lnr-bus", "lnr lnr-car", "lnr lnr-train", "lnr lnr-bicycle", "lnr lnr-wheelchair", "lnr lnr-select", "lnr lnr-earth", "lnr lnr-smile", "lnr lnr-sad", "lnr lnr-neutral", "lnr lnr-mustache", "lnr lnr-alarm", "lnr lnr-bullhorn", "lnr lnr-volume-high", "lnr lnr-volume-medium", "lnr lnr-volume-low", "lnr lnr-volume", "lnr lnr-mic", "lnr lnr-hourglass", "lnr lnr-undo", "lnr lnr-redo", "lnr lnr-sync", "lnr lnr-history", "lnr lnr-clock", "lnr lnr-download", "lnr lnr-upload", "lnr lnr-enter-down", "lnr lnr-exit-up", "lnr lnr-bug", "lnr lnr-code", "lnr lnr-link", "lnr lnr-unlink", "lnr lnr-thumbs-up", "lnr lnr-thumbs-down", "lnr lnr-magnifier", "lnr lnr-cross", "lnr lnr-menu", "lnr lnr-list", "lnr lnr-chevron-up", "lnr lnr-chevron-down", "lnr lnr-chevron-left", "lnr lnr-chevron-right", "lnr lnr-arrow-up", "lnr lnr-arrow-down", "lnr lnr-arrow-left", "lnr lnr-arrow-right", "lnr lnr-move", "lnr lnr-warning", "lnr lnr-question-circle", "lnr lnr-menu-circle", "lnr lnr-checkmark-circle", "lnr lnr-cross-circle", "lnr lnr-plus-circle", "lnr lnr-circle-minus", "lnr lnr-arrow-up-circle", "lnr lnr-arrow-down-circle", "lnr lnr-arrow-left-circle", "lnr lnr-arrow-right-circle", "lnr lnr-chevron-up-circle", "lnr lnr-chevron-down-circle", "lnr lnr-chevron-left-circle", "lnr lnr-chevron-right-circle", "lnr lnr-crop", "lnr lnr-frame-expand", "lnr lnr-frame-contract", "lnr lnr-layers", "lnr lnr-funnel", "lnr lnr-text-format", "lnr lnr-text-format-remove", "lnr lnr-text-size", "lnr lnr-bold", "lnr lnr-italic", "lnr lnr-underline", "lnr lnr-strikethrough", "lnr lnr-highlight", "lnr lnr-text-align-left", "lnr lnr-text-align-center", "lnr lnr-text-align-right", "lnr lnr-text-align-justify", "lnr lnr-line-spacing", "lnr lnr-indent-increase", "lnr lnr-indent-decrease", "lnr lnr-pilcrow", "lnr lnr-direction-ltr", "lnr lnr-direction-rtl", "lnr lnr-page-break", "lnr lnr-sort-alpha-asc", "lnr lnr-sort-amount-asc", "lnr lnr-hand", "lnr lnr-pointer-up", "lnr lnr-pointer-right", "lnr lnr-pointer-down", "lnr lnr-pointer-left"];

const RegistrationIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M2.15,18.89a6,6,0,0,1-1.06-.47A2.33,2.33,0,0,1,0,16.37q0-5.85,0-11.7A2.44,2.44,0,0,1,2.51,2.18q6.18,0,12.37,0A2.41,2.41,0,0,1,17.31,5l-1.17.13V4.66A1.24,1.24,0,0,0,14.83,3.4H2.59A1.27,1.27,0,0,0,1.25,4.73V16.35a1.27,1.27,0,0,0,1.31,1.31H14.85a1.24,1.24,0,0,0,1.29-1.32c0-1,0-1.93,0-2.9V13l1.21-.14v3.59a2.39,2.39,0,0,1-2,2.41.69.69,0,0,0-.15.07ZM20,7.8C19.46,7.2,18.9,6.59,18.31,6a.48.48,0,0,0-.7.05l-.84.81L19,9.07l1-1Zm-3.9-.28L11,12.67a.43.43,0,0,0-.13.27c0,.69,0,1.38,0,2.11H13a.52.52,0,0,0,.27-.15l5.15-5.15L16.18,7.49ZM12,13.25c0-.58.07-.75.84-1.37L14,13.13c-.38.29-.61,1-1.39.77l0-.65Zm4.29-4.36c-.88.88-1.76,1.77-2.66,2.65-.07.07-.22.06-.33.1,0-.13,0-.3.07-.37.88-.9,1.78-1.79,2.67-2.68a.43.43,0,0,1,.28-.11c.05,0,.1.11.16.18A2.59,2.59,0,0,1,16.27,8.89ZM3.5,5.91c-.45,0-.68.19-.7.56s.24.66.78.66H14.06c.5,0,.81-.24.81-.62s-.29-.6-.8-.6H3.5Zm7.71,4.91c.47,0,.74-.23.74-.62s-.28-.61-.75-.61H3.53c-.47,0-.74.24-.73.63s.25.6.74.6h7.67ZM3.46,13.33c-.42,0-.64.2-.66.56a.57.57,0,0,0,.61.65c1.27,0,2.53,0,3.8,0a.61.61,0,1,0,0-1.21H3.46Z"}));
const LoginIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M17.64,8.24c-.58,0-1.15,0-1.72,0-.13,0-.29-.21-.35-.35a6.73,6.73,0,0,0-5.49-4.46A6.81,6.81,0,0,0,2.27,9.33,6.77,6.77,0,0,0,8,17a6.84,6.84,0,0,0,7.53-4.36l.17-.45H17.6c.18,3-4.13,6.6-8.09,6.85A8.84,8.84,0,0,1,8.63,1.39,8.71,8.71,0,0,1,17.64,8.24Zm-10.51,2L12,14.12V11.18h7.81v-2H12V6.3Z"}));
const ChartIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M18.37,18.14H14.69V16q0-5.69,0-11.37c0-1,.31-1.32,1.3-1.32.48,0,1,0,1.44,0a1,1,0,0,1,1,1c0,4.55,0,9.11,0,13.67C18.41,18,18.39,18.05,18.37,18.14ZM14,10a1,1,0,0,0-1-.92H11.52c-.87,0-1.25.36-1.25,1.22v7.86H14C14,15.38,14,12.68,14,10ZM5.16,13a.88.88,0,0,0-.94-.84c-.58,0-1.17,0-1.76,0a.93.93,0,0,0-1,1c0,.94,0,1.89,0,2.83v2.17H5.17C5.17,16.38,5.19,14.67,5.16,13Zm13.45,6.56c.3,0,.79.19.78-.36s-.48-.32-.75-.32H2.15c-.34,0-.68,0-1,0s-.64-.11-.65.34.36.35.63.36h17.5ZM12.2,3.2C10.56,5.28,8.9,7.35,7.29,9.45a.6.6,0,0,1-.88.22C5,9.07,3.67,8.48,2.28,7.91c-.19-.08-.61-.06-.67,0-.21.38.18.45.43.56l4.27,1.82c.28.12.58.34.83.29s.35-.39.53-.61l0,0C9,8.37,10.23,6.76,11.5,5.16L13.25,3l.73.51.34-2.41L12.05,2l.67.56ZM9.61,15.8a1,1,0,0,0-.94-1c-.62,0-1.25,0-1.87,0a.88.88,0,0,0-.91.81c-.06.82,0,1.65,0,2.5H9.61C9.61,17.32,9.63,16.56,9.61,15.8Z"}));
const ContentIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M5.4,1.33V.22h9.2V1.33h2.2a.78.78,0,0,1,.88.89V18.87a.79.79,0,0,1-.88.89H3.17a.77.77,0,0,1-.85-.85V2.23c0-.62.29-.9.91-.9S4.65,1.33,5.4,1.33ZM16.13,18.22V2.88H14.6V4.8H5.39V2.88H3.88V18.22ZM5.41,10.53H10V5.94H5.41Zm0,1.56V13.6H14.6V12.09Zm9.19,4.58V15.16H5.4v1.51ZM11.55,6V7.46h3V6Zm0,3.07v1.51h3V9Z"}));
const CounterIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M10,4.67h8.81c.64,0,.73.08.73.7v9.78c0,.56-.1.67-.65.67H1c-.52,0-.63-.12-.63-.65V5.31c0-.54.11-.64.67-.64Zm2,5.62a13.59,13.59,0,0,0-.41-1.55,1.64,1.64,0,0,0-1.64-1,1.67,1.67,0,0,0-1.64,1,3.72,3.72,0,0,0,0,3.16,1.64,1.64,0,0,0,1.7,1,1.62,1.62,0,0,0,1.57-1.06A12.62,12.62,0,0,0,12,10.29ZM5.7,10.22a12.35,12.35,0,0,0-.53-1.69,1.58,1.58,0,0,0-1.65-.86A1.59,1.59,0,0,0,2,8.61a3.7,3.7,0,0,0,0,3.26,1.63,1.63,0,0,0,1.56,1,1.61,1.61,0,0,0,1.62-.92A13.39,13.39,0,0,0,5.7,10.22Zm.72-4.74V15h.72V5.48Zm6.36,0V15h.73V5.5Zm1.77.35a9.76,9.76,0,0,0,0,1.09,2,2,0,0,0,2.27,1.9,1.73,1.73,0,0,0,1.36-2.62.61.61,0,0,0-.62-.35c-.43,0-.86,0-1.28,0A6.91,6.91,0,0,0,15.5,6C15.3,5.64,14.89,6,14.55,5.85Zm3.64,4.42H14.75v.83h2.46l-1.83,3.51a6,6,0,0,1,.6,0,.56.56,0,0,0,.67-.4c.42-.89.86-1.77,1.33-2.63A2.19,2.19,0,0,0,18.19,10.27ZM10.67,9.1a.73.73,0,0,0-1.41,0,3.47,3.47,0,0,0,0,2.46.7.7,0,0,0,.7.51.71.71,0,0,0,.69-.53c.12-.39.18-.8.27-1.21C10.84,9.9,10.78,9.49,10.67,9.1ZM2.92,11.46a.73.73,0,0,0,1.4,0A3.42,3.42,0,0,0,4.33,9a.72.72,0,0,0-.71-.51.7.7,0,0,0-.7.52c-.12.39-.17.8-.26,1.21C2.75,10.66,2.8,11.07,2.92,11.46ZM16.53,6.28a.86.86,0,0,0-.89.87.87.87,0,0,0,.9.86.86.86,0,0,0,.89-.87A.85.85,0,0,0,16.53,6.28Z"}));
const EditProfileIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M11.88,12.11c-.2.22-.4.43-.61.63L9.42,14.57a2.33,2.33,0,0,0-.54,1.06c-.27.95-.55,1.89-.83,2.84a2,2,0,0,0-.05.85v0c-.29,0-.59,0-.88,0-.53,0-1.06-.05-1.59-.1a13,13,0,0,1-3.09-.59,3.92,3.92,0,0,1-1.35-.72A1.62,1.62,0,0,1,.47,16.6c0-.23,0-.46,0-.68A2,2,0,0,1,1,14.82a6.7,6.7,0,0,1,1.55-1.31A15.89,15.89,0,0,1,5.72,12a1.22,1.22,0,0,0,.81-1.51,1.11,1.11,0,0,0-.24-.5A7.72,7.72,0,0,1,4.46,6.43a6.27,6.27,0,0,1,0-3.16A4,4,0,0,1,6.75.55,4.63,4.63,0,0,1,9,.17a4.39,4.39,0,0,1,2.11.68,4.14,4.14,0,0,1,1.73,2.39A6.17,6.17,0,0,1,13,5.84a8.1,8.1,0,0,1-1.59,3.71,1.94,1.94,0,0,1-.26.31,1.14,1.14,0,0,0-.34.67,1.72,1.72,0,0,0,0,.93.85.85,0,0,0,.54.49ZM15,11.2l0,.06-2.23,2.2c-.7.69-1.39,1.39-2.1,2.08a.77.77,0,0,0-.19.28c-.29,1-.59,2.05-.89,3.07a.37.37,0,0,0,0,.18.4.4,0,0,0,.54.33l3.08-.91a.56.56,0,0,0,.2-.11l.78-.78L17,14.72l.74-.74Zm-3.84,5.06a.53.53,0,0,0,.51.21l.66.06h.06c0,.25,0,.49,0,.74a.45.45,0,0,0,.19.43.73.73,0,0,1,.08.09l-.72.21-.9.27a.1.1,0,0,1-.13,0,.43.43,0,0,0-.09-.1.38.38,0,0,1-.15-.5c.16-.46.28-.93.42-1.41C11.1,16.25,11.11,16.25,11.11,16.26ZM19.54,12a.91.91,0,0,0,0-1.24q-.69-.72-1.41-1.41a.87.87,0,0,0-1-.14,5.72,5.72,0,0,0-.65.55l2.65,2.65A5.74,5.74,0,0,0,19.54,12Zm-4.16-1.26,2.77,2.78.61-.58L16,10.16Z"}));
const ForgetPasswordIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M4.5,6.61A5.87,5.87,0,0,1,10.23,0,5.89,5.89,0,0,1,16,6.6l.32.09a1.88,1.88,0,0,1,1.55,2c0,2,0,4,0,5.94,0,1.11,0,2.21,0,3.31A1.87,1.87,0,0,1,15.77,20H4.69a1.86,1.86,0,0,1-2.06-2.07c0-3,0-5.93,0-8.89C2.63,7.51,3,7,4.5,6.61Zm2,0H14c.39-3.28-1.94-4.75-3.78-4.74C7.78,1.89,6.22,3.84,6.45,6.62Zm.85,4.56H9.13c0-.11,0-.2.06-.28a1,1,0,0,1,1.3-.8,1,1,0,0,1,.69,1.22,2.24,2.24,0,0,1-.47.87c-.33.41-.74.76-1.05,1.17a2.4,2.4,0,0,0-.43,1.8h1.65a2.23,2.23,0,0,1,.85-1.74,8.17,8.17,0,0,0,.91-.95,2.41,2.41,0,0,0-1.3-3.78A5.47,5.47,0,0,0,9.49,8.6,2.38,2.38,0,0,0,7.3,11.18Zm2.73,7a1,1,0,0,0,1.09-.94,1,1,0,0,0-1-1.07,1,1,0,0,0-1.08,1A.93.93,0,0,0,10,18.15Z"}));
const ListingIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M19.48,1.44V18.58a1.38,1.38,0,0,1-.93.93H1.41a1.32,1.32,0,0,1-.93-1.42Q.5,10,.49,1.92C.49.93.91.51,1.9.51c5.38,0,10.77,0,16.16,0A1.32,1.32,0,0,1,19.48,1.44ZM10,18.76h8.11c.58,0,.65-.07.65-.65V2c0-.7,0-.74-.74-.74H1.9c-.58,0-.65.07-.65.66q0,8,0,16.09c0,.69.05.73.75.73ZM16.22,6.41c.39,0,.57-.16.56-.55s0-1,0-1.56-.16-.55-.53-.55H8.84a.44.44,0,0,0-.51.51c0,.52,0,1,0,1.56s.12.59.6.59h7.29Zm-7.33,7.2c-.39,0-.57.15-.56.54,0,.55,0,1.1,0,1.65a.41.41,0,0,0,.46.47h7.5c.34,0,.49-.17.49-.5,0-.5,0-1,0-1.51s-.1-.65-.63-.65H8.89Zm7.36-2.35c.38,0,.54-.15.53-.53,0-.53,0-1.06,0-1.6,0-.35-.15-.52-.51-.52H8.82a.43.43,0,0,0-.49.49c0,.54,0,1.07,0,1.6s.13.56.54.56c1.22,0,2.45,0,3.67,0S15,11.24,16.25,11.26ZM3.59,5.08A1.3,1.3,0,0,1,4.88,3.76,1.33,1.33,0,0,1,6.23,5.08,1.35,1.35,0,0,1,4.88,6.41,1.31,1.31,0,0,1,3.59,5.08Zm1.87,0a.57.57,0,0,0-.53-.58.59.59,0,0,0-.6.58.58.58,0,0,0,.55.57A.56.56,0,0,0,5.46,5.08Zm-.58,6.17A1.28,1.28,0,0,1,3.59,9.94,1.3,1.3,0,0,1,4.92,8.62,1.34,1.34,0,0,1,6.23,9.94,1.3,1.3,0,0,1,4.88,11.25Zm0-1.87a.54.54,0,0,0-.54.57.56.56,0,0,0,.56.56.58.58,0,0,0,.57-.6A.56.56,0,0,0,4.88,9.38Zm0,4.23a1.33,1.33,0,1,1,0,2.66,1.33,1.33,0,0,1,0-2.66Zm-.57,1.31a.59.59,0,0,0,.57.6A.58.58,0,0,0,5.46,15a.56.56,0,0,0-.56-.59A.58.58,0,0,0,4.33,14.92Z"}));
const ManageIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M.32,2.41c0-.17.08-.35.14-.53A2.24,2.24,0,0,1,2.48.4H16.06a2.25,2.25,0,0,1,2.26,1.68,3.05,3.05,0,0,1,.08.74c0,2.44,0,4.89,0,7.33a1.93,1.93,0,0,1,0,.23,5.16,5.16,0,0,0-.53-.26,6.67,6.67,0,0,0-.74-.29.24.24,0,0,1-.22-.28c0-2.24,0-4.48,0-6.73a1.3,1.3,0,0,0,0-.34.7.7,0,0,0-.58-.56,1.53,1.53,0,0,0-.3,0H2.7a.79.79,0,0,0-.88.87c0,.29,0,.58,0,.86q0,5.84,0,11.65a1.47,1.47,0,0,0,.05.42.77.77,0,0,0,.84.52H9.39c.12,0,.17,0,.19.16.09.38.2.76.3,1.14a1.67,1.67,0,0,0,0,.18H2.65A2.25,2.25,0,0,1,.38,15.93l-.06-.26ZM15.38,19.8A4.37,4.37,0,1,0,11,15.43,4.36,4.36,0,0,0,15.38,19.8Zm-.75-5.13V13.19h1.52v1.48h1.5V16.2H16.16v1.49H14.64V16.2h-1.5V14.67Zm-.09-8.22a.77.77,0,0,0,.85-.85V5a.75.75,0,0,0-.84-.83H7.93A.74.74,0,0,0,7.11,5c0,.17,0,.35,0,.52A.8.8,0,0,0,8,6.45h6.52Zm.85,2.43c0-.64-.28-.92-.92-.92H7.91a.76.76,0,0,0-.8.79c0,.19,0,.38,0,.57,0,.6.29.89.88.89,1.48,0,2.95,0,4.42,0a1.3,1.3,0,0,0,.55-.12,4.26,4.26,0,0,1,.82-.3c.52-.12,1.05-.2,1.62-.3ZM7.92,11.73a.74.74,0,0,0-.81.81,5.8,5.8,0,0,0,0,.59.77.77,0,0,0,.83.84h1.6a.16.16,0,0,0,.19-.13,5.93,5.93,0,0,1,1-2l.05-.09ZM3.34,9.08v.4a.73.73,0,0,0,.74.72H4.8a.75.75,0,0,0,.79-.76c0-.24,0-.48,0-.73A.75.75,0,0,0,4.84,8H4.08a.75.75,0,0,0-.74.77v.35Zm1.12,2.65H4a.73.73,0,0,0-.69.72c0,.27,0,.54,0,.8A.7.7,0,0,0,4,14a5.3,5.3,0,0,0,.89,0,.73.73,0,0,0,.72-.73q0-.39,0-.78a.75.75,0,0,0-.76-.72ZM3.34,5.27v.4A.74.74,0,0,0,4,6.39c.27,0,.54,0,.82,0a.72.72,0,0,0,.74-.72q0-.39,0-.78a.74.74,0,0,0-.75-.73,6.77,6.77,0,0,0-.78,0,.73.73,0,0,0-.72.74v.36Z"}));
const RecentActivityIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M9.32,19.53A22.47,22.47,0,0,1,7,19,9.49,9.49,0,0,1,.55,9.88c0-.15,0-.3,0-.47H.83l1.65.27A7.62,7.62,0,0,0,9.7,17.56,7.52,7.52,0,0,0,13.3,3.27,7.34,7.34,0,0,0,5,4.58l1.41-.19.26,1.68-5.48.8.44-5.39,1.69.14L3.16,3.48c.39-.34.71-.65,1.07-.92a9.15,9.15,0,0,1,9.34-1.29,9.2,9.2,0,0,1,5.86,7.44l.09.62v1.43c0,.11-.05.21-.07.32a9.25,9.25,0,0,1-6.14,7.84,17.69,17.69,0,0,1-2.56.61Zm1.85-9.43a.94.94,0,0,1-.24-.58c0-.88,0-1.77,0-2.65V4.64H9c0,1.9,0,3.77,0,5.63a.64.64,0,0,0,.2.39c1.27,1.33,2.56,2.64,3.84,4,.08.08.17.14.26.23L14.5,13.6C13.39,12.44,12.27,11.28,11.17,10.1Z"}));
const ResetPasswordIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M.19,9.64.25,9A9.37,9.37,0,0,1,1.51,5.2,9.64,9.64,0,0,1,6.54,1a9.43,9.43,0,0,1,3.68-.6,9.53,9.53,0,0,1,4.36,1.2l.06,0h0l.9-1.25,1.62,4.25L12.94,4l1-1.37a8.49,8.49,0,1,0-.37,15.13,8.41,8.41,0,0,0,4.27-4.62,8.55,8.55,0,0,0-.07-6.28l1.11-.46,0,.06a9.49,9.49,0,0,1,.67,2.79,9.49,9.49,0,0,1-1.85,6.59,9.44,9.44,0,0,1-6,3.78,9.39,9.39,0,0,1-7.06-1.33A9.51,9.51,0,0,1,.37,11.92c-.08-.43-.11-.87-.16-1.3,0,0,0-.07,0-.1ZM12.33,7.88a3,3,0,0,0-.19-1.11,2.14,2.14,0,0,0-1.32-1.4,2.94,2.94,0,0,0-1.38-.12,2.08,2.08,0,0,0-1.1.49,2.5,2.5,0,0,0-.79,1.42,4.78,4.78,0,0,0-.08.75c0,.32,0,.63,0,.95a1.31,1.31,0,0,0-.59.21,1.43,1.43,0,0,0-.61,1.23v3.2a2,2,0,0,0,0,.34,1.32,1.32,0,0,0,1.24,1.1h4.73a1.12,1.12,0,0,0,.59-.17,1.38,1.38,0,0,0,.69-1.23V10.26a1.15,1.15,0,0,0,0-.26,1.34,1.34,0,0,0-1.19-1.14Zm-3,5.83V12.29a.12.12,0,0,0-.07-.13L9.07,12a1,1,0,0,1-.13-1.46,1.19,1.19,0,0,1,1.17-.43,1.15,1.15,0,0,1,.93.77A1,1,0,0,1,10.69,12a1.33,1.33,0,0,1-.19.14.14.14,0,0,0-.08.16v1.4ZM8.7,8.86s0,0,0,0c0-.42,0-.84,0-1.27a1.12,1.12,0,0,1,.47-.91A1.3,1.3,0,0,1,10,6.44a2.21,2.21,0,0,1,.46.1,1,1,0,0,1,.62.85,3.82,3.82,0,0,1,0,.59v.88Z"}));
const SearchIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M12.16,14a8.45,8.45,0,0,1-3.4,1.23A7.5,7.5,0,0,1,.47,9,7.47,7.47,0,0,1,6.8.28a7.17,7.17,0,0,1,7.31,3.18,7.21,7.21,0,0,1,.36,8,.54.54,0,0,0,.12.77c1.58,1.55,3.14,3.12,4.7,4.69a1.53,1.53,0,0,1,.37,1.83,1.43,1.43,0,0,1-1.53.86,2.29,2.29,0,0,1-1.06-.57C15.52,17.54,14,16,12.5,14.46A4.07,4.07,0,0,1,12.16,14ZM2.52,7.71A5.39,5.39,0,1,0,8,2.36,5.41,5.41,0,0,0,2.52,7.71Z"}));
const KB_Icon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M10.1,10.1c-0.4,0-0.7,0-1.1,0c-0.1,0-0.3,0-0.4-0.1C8.4,9.9,8.3,9.7,8.4,9.5c0-0.1,0-0.2,0-0.3c0-0.3,0-0.6-0.1-0.8C8.1,8,7.9,7.6,7.7,7.3C7.6,7.1,7.5,6.9,7.4,6.7C6.7,5.6,7,4.2,7.8,3.3c0.7-0.8,1.5-1.1,2.5-1c0.5,0,1,0.1,1.5,0.4c0.6,0.4,1,0.9,1.3,1.6c0.3,0.9,0.2,1.8-0.2,2.6c-0.3,0.5-0.5,1-0.7,1.4c-0.2,0.3-0.2,0.7-0.1,1c0,0.1,0,0.2,0,0.3c0,0.1-0.1,0.3-0.2,0.3c-0.2,0.1-0.4,0.1-0.6,0.1C10.8,10.1,10.5,10.1,10.1,10.1C10.1,10.1,10.1,10.1,10.1,10.1z M7.8,4C7.6,4.3,7.3,5,7.4,5.6C7.5,6.3,7.9,7,8.1,7.1C7.5,6.1,7.4,5.1,7.8,4z"}),
        CreateElement('path', {d: "M4.2,15.7c-0.6-0.5-0.8-1.3-0.6-2.1c0.2-0.5,0.5-0.8,1-1c0.2-0.1,0.4-0.1,0.5-0.1c0.8-0.1,1.6-0.2,2.4-0.3c0,0,0.1,0,0.1,0c0.5,0.4,1.1,0.6,1.7,0.7c0.8,0.1,1.5,0,2.2-0.3c0.5-0.2,0.9-0.5,1.2-1c0-0.1,0.1-0.2,0.1-0.2c0,0,0.1-0.1,0.1-0.1c0.1,0,0.2,0,0.2,0c0.9,0.1,1.9,0.3,2.8,0.4c0,0,0.1,0,0.1,0c0.1,0,0.2,0.1,0.2,0.2c0,0.1,0,0.3-0.1,0.4c-0.1,0.1-0.2,0.1-0.3,0.1c-1.1,0.2-2.1,0.5-3.2,0.7c-1.3,0.3-2.6,0.6-4,0.9c-0.1,0-0.1,0-0.2,0c-1.2-0.3-2.4-0.5-3.6-0.8c-0.3-0.1-0.6,0.1-0.8,0.4c-0.1,0.3-0.1,0.5-0.1,0.8c0.1,0.4,0.3,0.6,0.7,0.7c1.2,0.3,2.5,0.7,3.7,1c0.1,0,0.1,0,0.2,0c1.8-0.4,3.6-0.8,5.3-1.3c0.6-0.1,1.2-0.3,1.8-0.4c0.1,0,0.3,0,0.4,0.1c0.1,0.1,0.1,0.2,0,0.4c0,0.1-0.1,0.1-0.2,0.2c-0.2,0.1-0.5,0.1-0.7,0.2c-1.3,0.3-2.5,0.6-3.8,0.9c-0.9,0.2-1.9,0.5-2.8,0.7c-0.1,0-0.2,0-0.2,0c-1.1-0.3-2.2-0.6-3.3-0.9c-0.1,0-0.3,0-0.4-0.1c-0.2,0-0.4,0-0.5,0.2c-0.1,0.1-0.2,0.2-0.2,0.4c-0.1,0.2-0.1,0.5-0.1,0.8c0,0.4,0.2,0.6,0.6,0.7c0.8,0.2,1.5,0.4,2.3,0.6c0.5,0.2,1.1,0.3,1.6,0.4c0.1,0,0.1,0,0.2,0c1.8-0.5,3.6-0.9,5.4-1.4c0.6-0.1,1.1-0.3,1.7-0.4c0.3-0.1,0.5,0.1,0.4,0.4c0,0.1-0.1,0.2-0.1,0.2C16.2,18,16.1,18,16,18c-1.5,0.4-3.1,0.8-4.6,1.2c-0.8,0.2-1.7,0.4-2.5,0.6c-0.1,0-0.1,0-0.2,0c-1.4-0.4-2.7-0.7-4.1-1.1c-0.6-0.2-1-0.7-1.1-1.3c0-0.3,0-0.6,0.1-0.9C3.8,16.1,3.9,15.9,4.2,15.7z"}),
        CreateElement('path', {d: "M8.7,11.1C8.7,11,8.7,11,8.7,11.1c-0.3-0.2-0.3-0.4-0.1-0.6c0.1-0.1,0.2-0.1,0.4-0.1c0.7,0,1.4,0,2.1,0c0.1,0,0.2,0,0.4,0c0.1,0,0.1,0,0.2,0.1c0.2,0.1,0.2,0.4,0,0.5c0,0-0.1,0-0.1,0.1c0,0,0,0,0,0c0.2,0.2,0.2,0.5-0.1,0.6c-0.1,0-0.2,0-0.3,0.1c-0.1,0-0.1,0-0.1,0.1c0,0.1-0.1,0.3-0.1,0.4c0,0.1-0.1,0.2-0.2,0.2c-0.4,0-0.8,0-1.3,0c-0.1,0-0.2,0-0.2-0.2c0-0.1-0.1-0.3-0.1-0.4c0-0.1,0-0.1-0.1-0.1c-0.1,0-0.2,0-0.3-0.1C8.5,11.5,8.5,11.3,8.7,11.1C8.7,11.1,8.7,11.1,8.7,11.1z"}),
        CreateElement('path', {d: "M10.2,0.2c0.1,0.4,0.1,0.8,0.2,1.2c-0.1,0-0.3,0-0.4,0C10,1,10.1,0.6,10.2,0.2C10.2,0.2,10.2,0.2,10.2,0.2z"}),
        CreateElement('path', {d: "M6.5,4.6c0,0.1-0.1,0.3-0.1,0.4C6,4.8,5.7,4.6,5.3,4.5c0,0,0,0,0,0C5.7,4.5,6.1,4.5,6.5,4.6z"}),
        CreateElement('path', {d: "M13,3c0.3-0.2,0.7-0.4,1-0.6c0,0,0,0,0,0c-0.2,0.3-0.5,0.6-0.8,1C13.2,3.2,13.1,3.1,13,3z"}),
        CreateElement('path', {d: "M6.8,3.3C6.6,3,6.3,2.7,6.1,2.4c0,0,0,0,0,0c0.3,0.2,0.7,0.4,1,0.6C7,3.1,6.9,3.2,6.8,3.3z"}),
        CreateElement('path', {d: "M8.5,1.8C8.4,1.8,8.3,1.9,8.1,1.9C8,1.5,8,1.1,7.9,0.7c0,0,0,0,0,0C8.1,1.1,8.3,1.4,8.5,1.8z"}),
        CreateElement('path', {d: "M14.9,4.5c-0.4,0.2-0.7,0.4-1.1,0.5c0-0.1-0.1-0.3-0.1-0.4C14.1,4.5,14.5,4.5,14.9,4.5C14.9,4.4,14.9,4.5,14.9,4.5z"}),
        CreateElement('path', {d: "M11.8,1.8c0.2-0.3,0.4-0.7,0.7-1c0,0,0,0,0,0c-0.1,0.4-0.2,0.8-0.3,1.2C12,1.9,11.9,1.8,11.8,1.8z"}),
        );

const CaseDeflectionIcon = CreateElement('svg', {width: 20, height: 20}, CreateElement('path', {d: "M3.8,12.6c-0.5,0-0.9,0-1.2,0c-1.2,0-2.2-0.9-2.2-2.2c0-2.7,0-5.4,0-8.1c0-1.2,0.9-2.1,2.1-2.1c4.2,0,8.3,0,12.5,0c1.2,0,2.1,0.9,2.1,2c0,2.1,0,4.1,0,6.2c0,0.1,0,0.1,0,0.2c-0.9,0-1.7,0-2.6,0c-1,0-2.1,0-3.1,0c-1.1,0-1.9,0.8-1.9,1.9c0,0.7,0,1.3,0,2.1c-0.1,0-0.2,0-0.3,0c-0.9-0.2-1.5,0.2-2,1c-0.7,1.1-1.5,2.1-2.3,3.1c-0.1,0.2-0.4,0.3-0.7,0.4c-0.1-0.2-0.3-0.5-0.3-0.7C3.7,15.1,3.8,13.9,3.8,12.6z M10.9,4.4c0-1.3-0.8-2.3-2.2-2.5c-1.3-0.2-2.6,0.4-3,1.6C5.7,3.7,5.6,3.9,5.7,4.1c0,0.1,0.2,0.3,0.3,0.3c0.1,0,0.3-0.2,0.4-0.3C6.5,4,6.5,3.9,6.6,3.8c0.3-0.7,1.2-1.2,2.1-1C9.6,3,10,3.6,10,4.5c0,0.8-0.4,1.5-1,2C8.3,7.1,7.8,7.9,7.8,9c0,0.2,0.3,0.3,0.4,0.5C8.4,9.3,8.7,9.2,8.7,9c0-0.9,0.4-1.5,1-2C10.5,6.3,10.9,5.5,10.9,4.4z M8.9,10.4C8.6,10.2,8.4,9.9,8.3,10c-0.2,0-0.4,0.2-0.4,0.4c0,0.1,0.2,0.4,0.4,0.4C8.4,10.8,8.6,10.6,8.9,10.4z"}),
        CreateElement('path', {d: "M17.8,17.6c0,0.5,0,1,0,1.5c0,0.2-0.1,0.5-0.3,0.6c-0.1,0.1-0.4-0.1-0.6-0.2c-0.6-0.5-1.1-1.1-1.7-1.6c-0.2-0.2-0.5-0.3-0.8-0.3c-1,0-2,0-3,0c-1,0-1.6-0.6-1.6-1.6c0-1.8,0-3.6,0-5.3c0-1,0.6-1.5,1.5-1.5c2.3,0,4.5,0,6.8,0c1,0,1.5,0.6,1.5,1.5c0,1.8,0,3.5,0,5.3c0,1.1-0.5,1.6-1.6,1.6C18,17.5,17.9,17.5,17.8,17.6z M15.1,12.6c0-0.5,0-1.1,0-1.6c0-0.2-0.2-0.5-0.4-0.5c-0.3-0.1-0.5,0.2-0.5,0.5c0,1.1,0,2.2,0,3.3c0,0.2,0.2,0.5,0.4,0.5c0.3,0.1,0.5-0.2,0.5-0.5C15.1,13.7,15.1,13.1,15.1,12.6z M14.6,16.4c0.2-0.3,0.4-0.5,0.4-0.7c0-0.1-0.3-0.4-0.4-0.4c-0.1,0-0.4,0.2-0.4,0.4C14.2,15.9,14.4,16.1,14.6,16.4z"})
        );

const RadioButtonWithImage = (props) => {

    var label = props.label,
            className = props.className,
            selected = props.selected,
            help = props.help,
            onChange = props.onChange,
            props$options = props.options,
            options = props$options === void 0 ? [] : props$options;
    var instanceId = randomIntFromInterval(1, 200);
    var id = "inspector-radio-control-".concat(instanceId);
    var onChangeValue = function onChangeValue(event) {
        return onChange(event.target.value);
    };
    return  CreateElement("div", {
        label: label,
        id: id,
        help: help,
        className: className + 'components-radio-control',
    }, CreateElement('p', {className: 'sfcp-text'},
            CreateElement('label', {}, label)),
            CreateElement('div', {className: "image-option-wrapper"}, options.map(function (option, index) {
                return CreateElement("label", {
                    key: "".concat(id, "-").concat(index),
                    className: "components-radio-control__option"
                }, CreateElement("input", {
                    id: "".concat(id, "-").concat(index),
                    className: "components-radio-control__input imageradio",
                    type: "radio",
                    name: id,
                    value: option.value,
                    onChange: onChangeValue,
                    checked: option.value === selected,
                    "aria-describedby": !!help ? "".concat(id, "__help") : undefined
                }), CreateElement("img", {
                    htmlFor: "".concat(id, "-").concat(index),
                    src: option.image
                }))
            }))
            );
}


addFilter("blocks.getSaveElement", "sfcp-extendblock", getSaveElementCallback);
addFilter("blocks.getBlockAttributes", "sfcp-setdefault", sfcpSetDefaultValues);
function sfcpSetDefaultValues(blockArgs, BlockAtts) {

    if (BlockAtts.name === 'sfcp/portal-counter-block') {
        let blockStyle = blockArgs.view_style;
        blockArgs.hoverState = false;
        var defaultStyles = DefaultBlockColors.filter(obj => {
            return obj[blockStyle];
        });
        if (!blockArgs.block_color) {
            blockArgs.block_color = defaultStyles[0][blockStyle].background_color;
        }

        if (!blockArgs.block_hover_color) {
            blockArgs.block_hover_color = defaultStyles[0][blockStyle].background_hover_color;
        }

        if (!blockArgs.font_color) {
            blockArgs.font_color = defaultStyles[0][blockStyle].font_color;
        }

        if (!blockArgs.font_hover_color) {
            blockArgs.font_hover_color = defaultStyles[0][blockStyle].font_hover_color;
        }

        if (!blockArgs.selected_icon) {
            blockArgs.selected_icon = fnt_icons_categorized[0];
        }
    }
    return blockArgs;
}

function getSaveElementCallback(element, blockType, attributes) {

    if ("sfcp/portal-counter-block" === blockType.name) {

        if (!("counter_block_id" in attributes)) {
            attributes.random_number = 'icon_picker_' + randomIntFromInterval(1, 1000);
            attributes.counter_block_id = 'counter_block_id_' + randomIntFromInterval(1, 1000);
        }
        var defaultStyles = DefaultBlockColors.filter(obj => {
            return obj[attributes.view_style];
        });
        if (!attributes.block_color) {
            attributes.block_color = defaultStyles[0][attributes.view_style].background_color;
        }

        if (!attributes.block_hover_color) {
            attributes.block_hover_color = defaultStyles[0][attributes.view_style].background_hover_color;
        }

        if (!attributes.font_color) {
            attributes.font_color = defaultStyles[0][attributes.view_style].font_color;
        }

        if (!attributes.font_hover_color) {
            attributes.font_hover_color = defaultStyles[0][attributes.view_style].font_hover_color;
        }

        attributes.hoverState = false;
    }

    if ("sfcp/chart-block" === blockType.name && !("chart_block_id" in attributes) ) {
        attributes.chart_block_id = 'chart_block_' + randomIntFromInterval(1, 1000);
    }

    if ("sfcp/portal-recent-activity" === blockType.name) {

        if (!attributes.dropdown_field_color) {
            attributes.dropdown_field_color = colorSamples[0].color;
        }

        if (!("activity_block_id" in attributes)) {
            attributes.activity_block_id = 'activity_block_' + randomIntFromInterval(1, 1000);
        }
    }


    return element;
}

function sf_get_module_fields(module_name, field_type) {

    var module_data;
    jQuery.ajax({
        type: 'POST',
        url: my_ajax_object.ajax_url,
        async: false,
        data: {'action': 'bcp_get_fields', 'module_name': module_name, 'field_type': field_type},
        dataType: 'json',
        success: function (response) {
            module_data = response;
        }
    });
    return module_data;
}

function randomIntFromInterval(min = 1, max) { // min and max included 
    return Math.floor(Math.random() * (max - min + 1) + min);
}

/*
 * Portal Registration Block
 */

registerBlockType('sfcp/portal-registration', {
    title: __('Registration', 'bcp_portal'),
    icon: RegistrationIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        'sign_up_title': {
            type: 'string',
            default: 'Portal Sign Up'
        },
        capcha_enable: {
            type: 'boolean',
            default: false
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        }
    },
    edit: function (props) {

        const {
            sign_up_title,
            capcha_enable,
            view_style
        } = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-registration'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Registration', 'bcp_portal'),
                                ),
                                (sign_up_title ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, sign_up_title),
                                        ) : ''),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': RegistrationImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Registration ', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Registration Label', 'bcp_portal'),
                                                    value: sign_up_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({sign_up_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable Recaptcha ?', 'bcp_portal'),
                                                    checked: capcha_enable,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({capcha_enable: new_value});
                                                    },
                                                    help: (capcha_enable ? __('Recaptcha will be enabled on this page.', 'bcp_portal') : __('Recaptcha will not be enabled on this page.', 'bcp_portal'))
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/signup/signup_style_1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/signup/signup_style_2.png',
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/signup/signup_style_3.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        ))
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 *  Portal Login Block 
 */

registerBlockType('sfcp/portal-login', {
    title: __('Login', 'bcp_portal'),
    icon: LoginIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        username_text: {
            type: 'string',
            default: 'Portal Username'
        },
        password_text: {
            type: 'string',
            default: 'Portal Password'
        },
        login_button_text: {
            type: 'string',
            default: 'Login'
        },
        show_forgot_password: {
            type: 'boolean',
            default: true
        },
        signup_enable: {
            type: 'boolean',
            default: true
        },
        capcha_enable: {
            type: 'boolean',
            default: false
        },
    },
    edit: function (props) {

        const {
            username_text,
            password_text,
            login_button_text,
            show_forgot_password,
            signup_enable,
            capcha_enable,
        } = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-login-details'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Login', 'bcp_portal'),
                                ),
                                (username_text ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, username_text),
                                        CreateElement('span', {})) : ''),
                                (password_text ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, password_text),
                                        CreateElement('span', {})
                                        ) : ''),
                                login_button_text ? CreateElement('p', {className: 'sfcp-text sfcp-button'}, login_button_text) : ''),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Salesforce Login', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Username Label', 'bcp_portal'),
                                                    value: username_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({username_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Password Label', 'bcp_portal'),
                                                    value: password_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({password_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Login Button Label', 'bcp_portal'),
                                                    value: login_button_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({login_button_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable Forget password?', 'bcp_portal'),
                                                    checked: show_forgot_password,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({show_forgot_password: new_value});
                                                    },
                                                    help: (show_forgot_password ? __('Forget password page link will be visible.', 'bcp_portal') : __('Forget password page link will not be visible.', 'bcp_portal'))
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable signup?', 'bcp_portal'),
                                                    checked: signup_enable,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({signup_enable: new_value});
                                                    },
                                                    help: (signup_enable ? __('Portal Registration page link will be visible.', 'bcp_portal') : __('Portal Registration page link will not be visible.', 'bcp_portal'))
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable Recaptcha ?', 'bcp_portal'),
                                                    checked: capcha_enable,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({capcha_enable: new_value});
                                                    },
                                                    help: (capcha_enable ? __('Recaptcha will be enabled on this page.', 'bcp_portal') : __('Recaptcha will not be enabled on this page.', 'bcp_portal'))
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 * Portal Forget Password Block
 */

registerBlockType('sfcp/portal-forgot-password', {
    title: __('Forget Password', 'bcp_portal'),
    icon: ForgetPasswordIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        email_text: {
            type: 'string',
            default: 'Email Address'
        },
        submit_button_text: {
            type: 'string',
            default: 'Submit'
        },
        capcha_enable: {
            type: 'boolean',
            default: false
        }
    },
    edit: function (props) {

        const {
            email_text,
            submit_button_text,
            capcha_enable
        } = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block sfcp-forget-password'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Forget Password', 'bcp_portal'),
                                ),
                                (email_text ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, email_text),
                                        CreateElement('span', {})) : ''),
                                submit_button_text ? CreateElement('p', {className: 'sfcp-text sfcp-button'}, submit_button_text) : ''),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Salesforce Forget Password', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Email Label ', 'bcp_portal'),
                                                    value: email_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({email_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Submit Button Label', 'bcp_portal'),
                                                    value: submit_button_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({submit_button_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable Recaptcha ?', 'bcp_portal'),
                                                    checked: capcha_enable,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({capcha_enable: new_value});
                                                    },
                                                    help: (capcha_enable ? __('Recaptcha will be enabled on this page.', 'bcp_portal') : __('Recaptcha will not be enabled on this page.', 'bcp_portal'))
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 * Portal Forget Password Block
 */

registerBlockType('sfcp/portal-reset-password', {
    title: __('Reset Password', 'bcp_portal'),
    icon: ResetPasswordIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        new_password_text: {
            type: 'string',
            default: 'Enter new passsword'
        },
        confirm_password_text: {
            type: 'string',
            default: 'Enter confirm password'
        },
        submit_button_text: {
            type: 'string',
            default: 'Submit'
        },
    },
    edit: function (props) {

        const {
            new_password_text,
            confirm_password_text,
            submit_button_text
        } = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block sfcp-reset-password'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Reset Password', 'bcp_portal'),
                                ),
                                (new_password_text ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, new_password_text),
                                        CreateElement('span', {})) : ''),
                                (confirm_password_text ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('label', {}, confirm_password_text),
                                        CreateElement('span', {})
                                        ) : ''),
                                submit_button_text ? CreateElement('p', {className: 'sfcp-text sfcp-button'}, submit_button_text) : ''),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Salesforce Reset Password', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('New Password Label ', 'bcp_portal'),
                                                    value: new_password_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({new_password_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Confirm Password Label ', 'bcp_portal'),
                                                    value: confirm_password_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({confirm_password_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Submit Button Label', 'bcp_portal'),
                                                    value: submit_button_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({submit_button_text: new_value});
                                                    }
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 * Portal Listing Module
 */

registerBlockType('sfcp/portal-list-block', {

    title: __('List Block', 'bcp_portal'),
    icon: ListingIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        pagination: {
            type: 'string',
            default: "10"
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        }
    },
    edit: function (props) {

        const {
            pagination,
            view_style
        } = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-listing'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Listing', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': ListingImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Listing', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-range'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Records show at most', 'bcp_portal'),
                                                    value: pagination ? pagination : 5,
                                                    options: [
                                                        {
                                                            label: '5',
                                                            value: '5'
                                                        },
                                                        {
                                                            label: '10',
                                                            value: '10'
                                                        },
                                                        {
                                                            label: '15',
                                                            value: '15'
                                                        },
                                                        {
                                                            label: '20',
                                                            value: '20'
                                                        },
                                                        {
                                                            label: '25',
                                                            value: '25'
                                                        },
                                                        {
                                                            label: '30',
                                                            value: '30'
                                                        },
                                                    ],
                                                    onChange: function (new_value) {
                                                        props.setAttributes({pagination: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/list/list_style_1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/list/list_style_2.png'
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/list/list_style_3.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        ))
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 * Portal Detail block
 */

registerBlockType('sfcp/portal-detail-block', {
    title: __('Detail Block', 'bcp_portal'),
    icon: 'universal-access-alt',
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        }
    },
    edit: function (props) {

        const {view_style} = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-login-details'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Detail Block', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': DetailImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Detail Block ', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/detail/detail_style_1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/detail/detail_style_2.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        ))
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/**
 * Portal Add/Edit Form Block
 */

registerBlockType('sfcp/portal-add-edit-block', {
    title: __('Manage Block (Add-Edit)', 'bcp_portal'),
    icon: ManageIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        }
    },
    edit: function (props) {

        const {view_style} = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-manage-block'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Manage Block', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': AddImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Manage Block ', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/add/add_style_1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/add/add_style_2.png'
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/add/add_style_3.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        ))
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/**
 * Portal Edit Profile Block
 */

registerBlockType('sfcp/portal-profile-block', {
    title: __('Edit Profile', 'bcp_portal'),
    icon: EditProfileIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        edit_profile_title: {
            type: 'string',
            default: 'Edit Profile'
        },
        change_password_text: {
            type: 'string',
            default: 'Change Password'
        },
        edit_profile_text: {
            type: 'string',
            default: 'Save'
        },
        change_password_save_label: {
            type: 'string',
            default: 'Update'
        }
    },
    edit: function (props) {
        const {view_style, edit_profile_title, change_password_text, change_password_save_label, edit_profile_text} = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-login-details'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Edit Profile', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': ProfileImages[view_style]})
                                        ) : '')
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Edit Profile Block ', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Edit Profile Label', 'bcp_portal'),
                                                    value: edit_profile_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({edit_profile_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Change Password Label', 'bcp_portal'),
                                                    value: change_password_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({change_password_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Edit Profile : Button Label', 'bcp_portal'),
                                                    value: edit_profile_text,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({edit_profile_text: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Change Password : Button Label', 'bcp_portal'),
                                                    value: change_password_save_label,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({change_password_save_label: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/profile/profile_style_1.png',
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/profile/profile_style_2.png',
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/profile/profile_style_3.png',
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        ))
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/**
 * Portal Global Search block
 */

registerBlockType('sfcp/portal-search-block', {
    title: __('Global Search', 'bcp_portal'),
    icon: SearchIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
    },
    edit: function (props) {

        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-content-block'},
                                CreateElement(
                                        'h6',
                                        {className: 'sfcp sfcp-login'},
                                        __('Portal Global Search', 'bcp_portal'),
                                        )
                                )
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/**
 * Portal Content block
 */

registerBlockType('sfcp/portal-content-block', {
    title: __('Portal Content Block', 'bcp_portal'),
    icon: ContentIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
    },
    edit: function (props) {

        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-content-block'},
                                CreateElement(
                                        'h6',
                                        {className: 'sfcp sfcp-login'},
                                        __('Portal Content Block', 'bcp_portal'),
                                        )
                                )
                        )
                );
    },
    save: function (props) {
        return null;
    }
});
/*
 * Portal Counter Block
 * Dynamic Blocks
 */

registerBlockType('sfcp/portal-counter-block', {
    title: __('Counter Block', 'bcp_portal'),
    icon: CounterIcon,
    category: 'salesforce',
    attributes: {
        selected_module: {
            type: 'string',
            default: sfcp_available_modules[0].value
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        module_fields: {
            type: 'string'
        },
        selected_field: {
            type: 'string'
        },
        module_title: {
            type: 'string'
        },
        field_options_loaded: {
            type: 'string',
            default: false
        },
        module_dropdown_data: {
            type: 'string'
        },
        selected_field_value: {
            type: 'string'
        },
        selected_icon: {
            type: 'string',
        },
        random_number: {
            type: 'string'
        },
        counter_block_id: {
            type: 'string'
        },
        block_color: {
            type: 'string',
        },
        font_color: {
            type: 'string',
        },
        block_hover_color: {
            type: 'string',
        },
        font_hover_color: {
            type: 'string',
        },
        hoverState: {
            type: 'boolean',
            default: false
        }
    },
    edit: withColors('formColor')(function (props) {
        const {view_style,
            selected_module,
            module_fields,
            module_title,
            selected_field,
            field_options_loaded,
            module_dropdown_data,
            selected_field_value,
            selected_icon,
            random_number,
            counter_block_id,
            block_color,
            block_hover_color,
            font_color,
            font_hover_color,
            hoverState,
        } = props.attributes;
        if (!counter_block_id) {
            props.setAttributes({counter_block_id: 'counter_block_id_' + randomIntFromInterval(1, 1000)});
        }

        toggleHover = function () {
            props.setAttributes({hoverState: !hoverState});
        };
        var module_available_fields;
        if (!field_options_loaded && selected_module) {
            module_available_fields = sf_get_module_fields(selected_module, 'counter');
            if (module_available_fields.fields_found) {
                props.setAttributes({
                    module_fields: module_available_fields.fields_data,
                    module_dropdown_data: module_available_fields.field_values
                });
                if (!selected_field) {
                    props.setAttributes({
                        selected_field: module_available_fields.fields_data[Object.keys(module_available_fields.fields_data)[0]].value,
                    });
                }

                if (!selected_field_value) {
                    props.setAttributes({
                        selected_field_value: module_available_fields.field_values[Object.keys(module_available_fields.field_values)[0]][0].value
                    });
                }
            }
            props.setAttributes({field_options_loaded: true});
        }

        var IconPickerTimeout = setInterval(function () {
            if (jQuery('.sfcp-select-icon').length) {
                if (!random_number) {
                    props.setAttributes({
                        random_number: 'icon_picker_' + randomIntFromInterval(1, 1000),
                        selected_icon: fnt_icons_categorized[0]
                    });
                }

                var FontIconPicker = jQuery("#" + random_number).fontIconPicker({
                    source: fnt_icons_categorized,
                    searchSource: fnt_icons_categorized,
                    iconsPerPage: 20,
                    emptyIcon: false
                });
                FontIconPicker.on('change', function () {
                    props.setAttributes({selected_icon: jQuery(this).val()});
                });
                clearInterval(IconPickerTimeout);
            }
        }, 1000);
        if (hoverState === true) {
            var counterMainStyle = {'background-color': block_hover_color, 'color': font_hover_color};
            var counterOneLinkColor = {'color': font_hover_color};
            var style3_icon = {'background-color': block_color};
            var style3_icon_text = {'color': block_hover_color};
            var style4_icon = {color: font_hover_color, border: '1px solid' + font_color, 'background-color': block_hover_color};
        } else {
            var counterMainStyle = {'background-color': block_color, 'color': font_color};
            var counterOneLinkColor = {'color': font_color};
            var style3_icon = {'background-color': block_hover_color};
            var style3_icon_text = {'color': '#ffffff'};
            var style4_icon = {color: font_color, border: '1px solid' + font_color};
        }

        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper counter-block-wrapper'},
                        CreateElement("div", {className: 'sfcp sfcp-main-wrapper counter-block-wrapper'},
                                (view_style === 'style_1' ? CreateElement("div", {id: "counter_block",
                                    onMouseEnter: this.toggleHover,
                                    onMouseLeave: this.toggleHover,
                                    className: "case-counter-block number-block number-incident-block block-style-1",
                                    style: counterMainStyle
                                }, CreateElement("div", {class: "card-body"},
                                        CreateElement("div", {class: "bcp-row"},
                                                CreateElement("div", {class: "bcp-col counter-name"},
                                                        CreateElement("h2", null, (module_title ? module_title : 'Counter title')),
                                                        CreateElement("h3", null, "99")
                                                        ),
                                                CreateElement("div", {class: "bcp-col-auto"},
                                                        CreateElement("span", {class: "lnr " + selected_icon, style: counterOneLinkColor}),
                                                        CreateElement("span", {class: "right-circle", style: {'background': block_hover_color}})
                                                        ),
                                                ),
                                        CreateElement("div", {class: "bcp-row"},
                                                CreateElement("div", {class: "bcp-col"},
                                                        CreateElement("a", {href: "#", style: counterOneLinkColor}, "View more")
                                                        )
                                                )
                                        )
                                        ) : ''),
                                (view_style === 'style_2' ? CreateElement("div", {
                                    style: counterMainStyle,
                                    id: "counter_block",
                                    className: "case-counter-block number-block number-incident-block block-style-2"
                                },
                                        CreateElement("div", {className: "card-body"},
                                                CreateElement("div", {className: "bcp-row"},
                                                        CreateElement("div", {className: "bcp-col counter-name"},
                                                                CreateElement("h2", null, (module_title ? module_title : 'Counter title')),
                                                                CreateElement("h3", null, "99")),
                                                        CreateElement("div", {className: "bcp-col-auto icon-block"},
                                                                CreateElement("span", {className: "lnr " + selected_icon, style: {color: 'white'}}),
                                                                CreateElement("a", {style: counterOneLinkColor, href: "javascript:void(0)"}, "View more"))
                                                        )
                                                )
                                        ) : ''),
                                (view_style === 'style_3' ? CreateElement("div", {
                                    onMouseEnter: this.toggleHover,
                                    onMouseLeave: this.toggleHover,
                                    style: counterMainStyle,
                                    id: "counter_block",
                                    className: "number-block number-incident-block counter-block-style-3"
                                },
                                        CreateElement("div", {className: "card-body"},
                                                CreateElement("div", {className: "bcp-row"},
                                                        CreateElement("div", {className: "bcp-col-auto icon-block 1", style: style3_icon},
                                                                CreateElement("span", {className: "lnr " + selected_icon, style: style3_icon_text})),
                                                        CreateElement("div", {className: "bcp-col counter-name"},
                                                                CreateElement("h2", null, (module_title ? module_title : 'Counter title')),
                                                                CreateElement("h3", null, "99"))),
                                                CreateElement("div", {className: "bcp-row"},
                                                        CreateElement("div", {className: "bcp-col text-right"},
                                                                CreateElement("a", {style: counterOneLinkColor, href: "javascript:void(0)"}, "View more"))
                                                        )
                                                )
                                        ) : ''),
                                (view_style === 'style_4' ? CreateElement("div", {
                                    onMouseEnter: this.toggleHover,
                                    onMouseLeave: this.toggleHover,
                                    style: {'background-color': block_color},
                                    id: "counter_block",
                                    className: "number-block number-incident-block counter-block-style-4"
                                },
                                        CreateElement("div", {className: "card-body"},
                                                CreateElement("a", {className: "lnr lnr-arrow-right", href: "#", style: {color: font_color}}),
                                                CreateElement("div", {className: "bcp-row"},
                                                        CreateElement("div", {className: "bcp-col-auto icon-block", style: style4_icon},
                                                                CreateElement("span", {className: "lnr " + selected_icon})),
                                                        CreateElement("div", {className: "bcp-col counter-name"},
                                                                CreateElement("h2", null, (module_title ? module_title : 'Counter title')),
                                                                CreateElement("h3", {style: style4_icon}, "99"))
                                                        )
                                                )
                                        ) : ''),
                                (view_style === 'style_5' ? CreateElement("div", {
                                    id: "counter_block",
                                    className: "number-block number-incident-block counter-block-style-5"
                                },
                                        CreateElement("div", {className: "card-body"},
                                                CreateElement("div", {className: "bcp-row"},
                                                        CreateElement("div", {className: "bcp-col-auto icon-block", style: counterMainStyle},
                                                                CreateElement("i", {className: "lnr " + selected_icon, "aria-hidden": "true"})),
                                                        CreateElement("div", {className: "bcp-col counter-name"},
                                                                CreateElement("h2", null, (module_title ? module_title : 'Counter title'))
                                                                )
                                                        )
                                                ),
                                        CreateElement("h3", {className: "counter-number-h3", style: counterMainStyle}, "99",
                                                CreateElement("a", {href: "#", className: "counter-right-icon"},
                                                        CreateElement("i", {className: "lnr lnr-chevron-right", "aria-hidden": "true"})
                                                        )
                                                )
                                        ) : ''),
                                (view_style === 'style_6' ? CreateElement("div", {
                                    id: "counter_block",
                                    class: "number-block number-incident-block counter-block-style-6"
                                },
                                        CreateElement("div", {class: "card-body"},
                                                CreateElement("div", {class: "bcp-row"},
                                                        CreateElement("div", {class: "bcp-col-auto icon-block", style: {'background-color': block_color, 'color': font_color}},
                                                                CreateElement("span", {class: "lnr " + selected_icon})
                                                                ),
                                                        CreateElement("div", {class: "bcp-col counter-name"},
                                                                CreateElement("h2", null, (module_title ? module_title : 'Counter title')),
                                                                CreateElement("h3", {class: "counter-number-h3"}, "99"))
                                                        )
                                                ),
                                        CreateElement("a", {href: "#"},
                                                CreateElement("i", {class: "lnr lnr-chevron-right", "aria-hidden": "true"})
                                                )
                                        ) : '')
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Counter Block', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Counter title', 'bcp_portal'),
                                                    value: module_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({module_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Module', 'bcp_portal'),
                                                    value: selected_module,
                                                    options: sfcp_available_modules,
                                                    onChange: (module_name) => {

                                                        props.setAttributes({selected_module: module_name});
                                                        module_available_fields = sf_get_module_fields(module_name, 'counter');
                                                        if (module_available_fields.fields_found) {
                                                            props.setAttributes({
                                                                module_fields: module_available_fields.fields_data,
                                                                module_dropdown_data: module_available_fields.field_values,
                                                                selected_field: module_available_fields.fields_data[0].value,
                                                                selected_field_value: module_available_fields.field_values[Object.keys(module_available_fields.field_values)[0]][0].value
                                                            });
                                                        } else {
                                                            props.setAttributes({
                                                                module_fields: null,
                                                                module_dropdown_data: null
                                                            });
                                                        }
                                                    }
                                                }
                                        )),
                                        module_fields ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Field', 'bcp_portal'),
                                                    value: selected_field,
                                                    options: module_fields,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({
                                                            selected_field: new_value,
                                                            selected_field_value: module_dropdown_data[new_value][0].value
                                                        });
                                                    }
                                                }
                                        )) : '',
                                        (field_options_loaded && module_dropdown_data ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select field value', 'bcp_portal'),
                                                    value: selected_field_value,
                                                    options: module_dropdown_data[selected_field],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({
                                                            selected_field_value: new_value
                                                        });
                                                    }
                                                }
                                        )) : ''),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/counter/counter_style_1.png',
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/counter/counter_style_2.png',
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/counter/counter_style_3.png',
                                                        }, {
                                                            label: 'Style 4',
                                                            value: 'style_4',
                                                            image: image_prefix + '/counter/counter_style_4.png',
                                                        }, {
                                                            label: 'Style 5',
                                                            value: 'style_5',
                                                            image: image_prefix + '/counter/counter_style_5.png',
                                                        }, {
                                                            label: 'Style 6',
                                                            value: 'style_6',
                                                            image: image_prefix + '/counter/counter_style_6.png',
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                TextControl,
                                                {
                                                    id: random_number,
                                                    className: 'sfcp-select-icon',
                                                    label: __('Select Icon', 'bcp_portal'),
                                                    value: selected_icon ? selected_icon : ''
                                                }
                                        )),
                                        ),
                                CreateElement(PanelColorSettings,
                                        {
                                            title: __('Color Settings', 'bcp_portal'),
                                            initialOpen: true,
                                            colorSettings: [
                                                {
                                                    colors: [],
                                                    value: block_color,
                                                    label: __('Background color', 'bcp_portal'),
                                                    onChange: function (new_color) {
                                                        props.setAttributes({block_color: new_color});
                                                    }
                                                },
                                                {
                                                    colors: [],
                                                    value: block_hover_color,
                                                    label: __('Background Hover color', 'bcp_portal'),
                                                    onChange: function (new_color) {
                                                        props.setAttributes({block_hover_color: new_color})
                                                    }
                                                },
                                                {
                                                    colors: [],
                                                    value: font_color,
                                                    label: __('Font color', 'bcp_portal'),
                                                    onChange: function (new_color) {
                                                        props.setAttributes({font_color: new_color})
                                                    }
                                                },
                                                {
                                                    colors: [],
                                                    value: font_hover_color,
                                                    label: __('Font hover color', 'bcp_portal'),
                                                    onChange: function (new_color) {
                                                        props.setAttributes({font_hover_color: new_color})
                                                    }
                                                }
                                            ]
                                        }),
                                ),
                        )
                );
    }),
    save: function (props) {
        return null;
    }
});
/*
 * Portal Recent Activity
 * Dynamic Blocks
 */
registerBlockType('sfcp/portal-recent-activity', {

    title: __('Recent Activity', 'bcp_portal'),
    icon: RecentActivityIcon,
    category: 'salesforce',
    attributes: {
        activity_block_id: {
            type: 'string',
        },
        selected_module: {
            type: 'string',
            default: sfcp_available_modules[0].value
        },
        module_title: {
            type: 'string',
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        field_options_loaded: {
            type: 'string',
            default: false
        },
        field1_options: {
            type: 'string',
        },
        field2_options: {
            type: 'string',
        },
        field3_options: {
            type: 'string',
        },
        field1_selected_option: {
            type: 'string',
        },
        field2_selected_option: {
            type: 'string',
        },
        field3_selected_option: {
            type: 'string',
        },
        dropdown_field_color: {
            type: 'string',
        },
    },
    edit: withColors('formColor')(function (props) {

        const {
            selected_module,
            module_title,
            view_style,
            field_options_loaded,
            field1_options,
            field2_options,
            field3_options,
            field1_selected_option,
            field2_selected_option,
            field3_selected_option,
            activity_block_id,
            dropdown_field_color
        } = props.attributes;
        if (!activity_block_id) {
            props.setAttributes({activity_block_id: 'activity_block_' + randomIntFromInterval(1, 1000)});
        }


        if (!field_options_loaded && selected_module) {
            module_available_fields = sf_get_module_fields(selected_module, 'recent_activity');
            if (module_available_fields.fields_found) {
                props.setAttributes({
                    selected_module: selected_module,
                    field1_options: module_available_fields.fields_data.all_field_data,
                    field2_options: module_available_fields.fields_data.dropdown_fields,
                    field3_options: module_available_fields.fields_data.date_fields
                });
                if (!field1_selected_option) {
                    props.setAttributes({
                        field1_selected_option: module_available_fields.fields_data.all_field_data[0].value,
                    });
                }
                if (!field2_selected_option) {
                    props.setAttributes({
                        field2_selected_option: module_available_fields.fields_data.dropdown_fields[0].value,
                    });
                }
                if (!field3_selected_option) {
                    props.setAttributes({
                        field3_selected_option: module_available_fields.fields_data.date_fields[0].value,
                    });
                }
            }
            props.setAttributes({field_options_loaded: true});
        }

        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-recent-activity'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Recent Activity', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': ActivityImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Recent Activity', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Module Heading', 'bcp_portal'),
                                                    value: module_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({module_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Module', 'bcp_portal'),
                                                    value: selected_module,
                                                    options: sfcp_available_modules,
                                                    onChange: (module_name) => {

                                                        props.setAttributes({selected_module: module_name});
                                                        module_available_fields = sf_get_module_fields(module_name, 'recent_activity');
                                                        var field2options =  module_available_fields.fields_data.dropdown_fields;
                                                        if(field2options == undefined || field2options == null){
                                                            field2options =  module_available_fields.fields_data.all_field_data[0].value;
                                                        }
                                                        if (module_available_fields.fields_found) {
                                                            props.setAttributes({
                                                                field1_options: module_available_fields.fields_data.all_field_data,
                                                                field2_options: module_available_fields.fields_data.dropdown_fields,
                                                                field3_options: module_available_fields.fields_data.date_fields,
                                                                field1_selected_option: module_available_fields.fields_data.all_field_data[0].value,
                                                                field2_selected_option: field2options,
                                                                field3_selected_option: module_available_fields.fields_data.date_fields[0].value,
                                                            });
                                                        } else {
                                                            props.setAttributes({
                                                                module_fields: null
                                                            });
                                                        }
                                                    }
                                                }
                                        )),
                                        (field1_options && field1_selected_option ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Field 1 (Any field informative)', 'bcp_portal'),
                                                    value: field1_selected_option,
                                                    options: field1_options,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({field1_selected_option: new_value});
                                                    },
                                                    help: __('Select Field 1 Any Informative Fields', 'bcp_portal'),
                                                }
                                        )) : ''),
                                        (field2_options && field2_selected_option ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Field 2 (Dropdown field)', 'bcp_portal'),
                                                    value: field2_selected_option,
                                                    options: field2_options,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({field2_selected_option: new_value});
                                                    },
                                                    help: __('Select Field 2 Any Dropdown Fields', 'bcp_portal'),
                                                }
                                        )) : ''),
                                        (field3_options && field3_selected_option ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Field 3 (Date field)', 'bcp_portal'),
                                                    value: field3_selected_option,
                                                    options: field3_options,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({field3_selected_option: new_value});
                                                    },
                                                    help: __('Select Field 3 Any Date Fields', 'bcp_portal'),
                                                }
                                        )) : ''),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/recent_activity/recent_activity_style_1.png',
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/recent_activity/recent_activity_style_2.png',
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: image_prefix + '/recent_activity/recent_activity_style_3.png',
                                                        }, {
                                                            label: 'Style 4',
                                                            value: 'style_4',
                                                            image: image_prefix + '/recent_activity/recent_activity_style_4.png',
                                                        }, {
                                                            label: 'Style 5',
                                                            value: 'style_5',
                                                            image: image_prefix + '/recent_activity/recent_activity_style_5.png',
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        )),
                                        ),
                                ((['style_3', 'style_4'].includes(view_style)) ? CreateElement(PanelColorSettings,
                                        {
                                            title: 'Color Settings',
                                            initialOpen: true,
                                            colorSettings: [
                                                {
                                                    colors: [],
                                                    value: dropdown_field_color,
                                                    label: 'Background color for Dropdown field',
                                                    onChange: function (new_color) {
                                                        props.setAttributes({dropdown_field_color: new_color});
                                                    }
                                                }
                                            ]
                                        }
                                ) : '')
                                ),
                        )
                );
    }),
    save: function (props) {
        return null;
    }
});
/**
 * Portal Pie chart block
 * Dynamic Blocks
 */

registerBlockType('sfcp/chart-block', {
    title: __('Chart Block', 'bcp_portal'),
    icon: ChartIcon,
    category: 'salesforce',
    attributes: {
        chart_block_id: {
            type: 'string',
        },
        selected_module: {
            type: 'string',
            default: ''
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        module_fields: {
            type: 'string'
        },
        selected_field: {
            type: 'string'
        },
        module_title: {
            type: 'string'
        },
        field_options_loaded: {
            type: 'string',
            default: false
        },
        legend_enable: {
            type: 'boolean',
            default: false
        }
    },
    edit: function (props) {
        const {
            view_style,
            selected_module,
            module_fields,
            module_title,
            selected_field,
            field_options_loaded,
            chart_block_id,
            legend_enable
        } = props.attributes;
        var module_available_fields, chart_demo_image;
        var post_submit_button = document.querySelector('.editor-post-publish-button');
        if (!chart_block_id) {
            props.setAttributes({chart_block_id: 'chart_block_' + randomIntFromInterval(1, 1000)});
        }

        chart_demo_image = legend_enable ? ChartImages['chart_images_with_legend'][view_style] : ChartImages['chart_images'][view_style];

        if (!field_options_loaded && selected_module) {
            module_available_fields = sf_get_module_fields(selected_module, 'charts');
            if (module_available_fields.fields_found) {
                props.setAttributes({
                    module_fields: module_available_fields.fields_data,
                });
                if (!selected_field) {
                    props.setAttributes({
                        selected_field: module_available_fields.fields_data[Object.keys(module_available_fields.fields_data)[0]].value
                    });
                }
            }
            props.setAttributes({field_options_loaded: true});
        }

        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-chart'}, CreateElement(
                                'h6', {className: 'sfcp sfcp-login'}, __('Portal Chart Block', 'bcp_portal')),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': chart_demo_image})
                                        ) : '')),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {'title': __('Chart Block ', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Chart title', 'bcp_portal'),
                                                    value: module_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({module_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Module', 'bcp_portal'),
                                                    value: selected_module,
                                                    options: sfcp_available_modules_chart,
                                                    onChange: (module_name) => {
                                                        props.setAttributes({selected_module: module_name});
                                                        module_available_fields = sf_get_module_fields(module_name, 'charts');
                                                        if (module_available_fields.fields_found && typeof module_available_fields.fields_data !== 'undefined') {
                                                            lockPageSave(false, 'bcp_chart_error', "Removed");
                                                            props.setAttributes({
                                                                module_fields: module_available_fields.fields_data,
                                                                selected_field: module_available_fields.fields_data[Object.keys(module_available_fields.fields_data)[0]].value
                                                            });
                                                        } else {
                                                            if (module_available_fields.no_fields_found) {
                                                                let actual_module_name = sfcp_available_modules_chart.find(o => o.value == module_name);
                                                                lockPageSave(true, 'bcp_chart_error', " No Dropdown fields available for " + actual_module_name.label, );
                                                            }
                                                            props.setAttributes({module_fields: null});
                                                        }
                                                    }
                                                }
                                        )),
                                        module_fields && selected_field ? CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Select Fields', 'bcp_portal'),
                                                    value: selected_field,
                                                    options: module_fields,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({
                                                            selected_field: new_value,
                                                        });
                                                    }
                                                }
                                        )) : '',
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: (legend_enable ? image_prefix + '/chart/chart_style_1-legend.png' : image_prefix + '/chart/chart_style_1.png'),
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: (legend_enable ? image_prefix + '/chart/chart_style_2-legend.png' : image_prefix + '/chart/chart_style_2.png'),
                                                        },
                                                        {
                                                            label: 'Style 3',
                                                            value: 'style_3',
                                                            image: (legend_enable ? image_prefix + '/chart/chart_style_3-legend.png' : image_prefix + '/chart/chart_style_3.png'),
                                                        }, {
                                                            label: 'Style 4',
                                                            value: 'style_4',
                                                            image: (legend_enable ? image_prefix + '/chart/chart_style_4-legend.png' : image_prefix + '/chart/chart_style_4.png'),
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-toggle'}, CreateElement(
                                                ToggleControl,
                                                {
                                                    label: __('Enable Legend?', 'bcp_portal'),
                                                    checked: legend_enable,
                                                    onChange: (new_value) => {
                                                        props.setAttributes({legend_enable: new_value});
                                                    },
                                                    help: (legend_enable ? __('Legend will be enabled on this block.', 'bcp_portal') : __('Legend will not be enabled on this block.', 'bcp_portal'))
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});

/*
 * Portal KB block
 */

registerBlockType('sfcp/portal-knowledgebase-block', {
    title: __('Knowledge base Block', 'bcp_portal'),
    icon: KB_Icon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        pagination: {
            type: 'string',
            default: "10"
        },
    },
    edit: function (props) {

        const {view_style, pagination} = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-login-details'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Knowledge Base Block', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': KBImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {title: __('Knowledge Base Block', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/knowledgebase/style-1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/knowledgebase/style-2.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-range'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Records show at most', 'bcp_portal'),
                                                    value: pagination ? pagination : 5,
                                                    options: [
                                                        {
                                                            label: '5',
                                                            value: '5'
                                                        },
                                                        {
                                                            label: '10',
                                                            value: '10'
                                                        },
                                                        {
                                                            label: '15',
                                                            value: '15'
                                                        },
                                                        {
                                                            label: '20',
                                                            value: '20'
                                                        },
                                                        {
                                                            label: '25',
                                                            value: '25'
                                                        },
                                                        {
                                                            label: '30',
                                                            value: '30'
                                                        },
                                                    ],
                                                    onChange: function (new_value) {
                                                        props.setAttributes({pagination: new_value});
                                                    }
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});

/*
 * Portal Casedeflection block
 */

registerBlockType('sfcp/portal-casedeflection-block', {
    title: __('Case Deflection Block', 'bcp_portal'),
    icon: CaseDeflectionIcon,
    category: 'salesforce',
    supports: {
        reusable: false,
        multiple: false,
    },
    attributes: {
        module_description: {
            type: 'string',
            default: "You may find probable solution you are looking, just search it or "
        },
        add_case_title: {
            type: 'string',
            default: " Add Case"
        },
        view_style: {
            type: 'string',
            default: DefaultDisplayStyle
        },
        pagination: {
            type: 'string',
            default: "10"
        },
    },
    edit: function (props) {

        const {module_description, add_case_title, view_style, pagination} = props.attributes;
        return (
                CreateElement(
                        Fragment,
                        {className: 'sfcp sfcp-main-wrapper'},
                        CreateElement('div', {className: 'sfcp-portal-block portal-login-details'}, CreateElement(
                                'h6',
                                {className: 'sfcp sfcp-login'},
                                __('Portal Case Deflection Block', 'bcp_portal'),
                                ),
                                (view_style ? CreateElement('p', {className: 'sfcp-text'},
                                        CreateElement('img', {className: 'sfcp-image sfcp-style', 'src': CaseDeflectionImages[view_style]})
                                        ) : ''),
                                ),
                        CreateElement(
                                InspectorControls,
                                {key: 'controls'},
                                CreateElement(
                                        PanelBody, {title: __('Case Deflection Block', 'bcp_portal'), initialOpen: true, className: 'sfcp-sidebar'},
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Description text', 'bcp_portal'),
                                                    value: module_description,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({module_description: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                                                TextControl,
                                                {
                                                    label: __('Add Case Heading', 'bcp_portal'),
                                                    value: add_case_title,
                                                    onChange: function (new_value) {
                                                        props.setAttributes({add_case_title: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                                                RadioButtonWithImage,
                                                {
                                                    label: __('Select view style', 'bcp_portal'),
                                                    selected: view_style,
                                                    options: [
                                                        {
                                                            label: 'Style 1',
                                                            value: 'style_1',
                                                            image: image_prefix + '/case-deflection/style-1.png'
                                                        },
                                                        {
                                                            label: 'Style 2',
                                                            value: 'style_2',
                                                            image: image_prefix + '/case-deflection/style-2.png'
                                                        }
                                                    ],
                                                    onChange: (new_value) => {
                                                        props.setAttributes({view_style: new_value});
                                                    }
                                                }
                                        )),
                                        CreateElement(PanelRow, {className: 'sfcp sfcp-range'}, CreateElement(
                                                SelectControl,
                                                {
                                                    label: __('Records show at most', 'bcp_portal'),
                                                    value: pagination ? pagination : 5,
                                                    options: [
                                                        {
                                                            label: '5',
                                                            value: '5'
                                                        },
                                                        {
                                                            label: '10',
                                                            value: '10'
                                                        },
                                                        {
                                                            label: '15',
                                                            value: '15'
                                                        },
                                                        {
                                                            label: '20',
                                                            value: '20'
                                                        },
                                                        {
                                                            label: '25',
                                                            value: '25'
                                                        },
                                                        {
                                                            label: '30',
                                                            value: '30'
                                                        },
                                                    ],
                                                    onChange: function (new_value) {
                                                        props.setAttributes({pagination: new_value});
                                                    }
                                                }
                                        )),
                                        ),
                                ),
                        )
                );
    },
    save: function (props) {
        return null;
    }
});

function MyDocumentSettingPlugin(props) {

    var postType = useSelect(function (select) {
        return select('core/editor').getCurrentPostType();
    }, []);

    var entityProp = useEntityProp('postType', postType, 'meta');
    var meta = entityProp[ 0 ];
    var setMeta = entityProp[ 1 ];
    var BcpFieldTypeValue = meta['_bcp_page_type'];
    var BcpModuleValue = meta['_bcp_modules'];

    return CreateElement(
            PluginDocumentSettingPanel,
            {
                className: 'bcp-pagetype-selection',
                title: __('Select Page Type', 'bcp_portal'),
            },
            CreateElement(PanelRow, {className: 'sfcp sfcp-select'}, CreateElement(
                    SelectControl,
                    {
                        label: __('Select Page Type', 'bcp_portal'),
                        value: BcpFieldTypeValue,
                        options: bcp_page_types,
                        onChange: function (new_page_type) {
                            let tmp_module_name = BcpModuleValue ? BcpModuleValue : sfcp_available_modules_all[0].value;
                            var PageExists = checkPortalPageExists(new_page_type, tmp_module_name);
                            lockPageSave(PageExists, 'bcp-page-type', `There is one already page created for this combination.`);
                            setMeta(Object.assign({}, meta, {
                                _bcp_page_type: new_page_type,
                                _bcp_portal_elements: true
                            }));
                        },
                        help: __('You can define any type of portal page. This will directly reflect on portal side.', 'bcp_portal')
                    }
            )),
            (BcpFieldTypeValue && (['bcp_list', 'bcp_add_edit', 'bcp_detail'].includes(BcpFieldTypeValue)) ? CreateElement(PanelRow, {className: 'sfcp-text'}, CreateElement(
                    SelectControl,
                    {
                        label: __('Select Module', 'bcp_portal'),
                        value: BcpModuleValue,
                        options: sfcp_available_modules_all,
                        onChange: function (new_module_name) {
                            var PageExists = checkPortalPageExists(BcpFieldTypeValue, new_module_name);
                            lockPageSave(PageExists, 'bcp-page-type', `There is one already page created for this combination.`);
                            setMeta(Object.assign({}, meta, {
                                _bcp_modules: new_module_name,
                                _bcp_portal_elements: true
                            }));
                        }
                    }
            )) : '')
            );
}

registerPlugin('bcp-pagetype-selection', {
    icon: null,
    render: MyDocumentSettingPlugin
});

function checkPortalPageExists(page_type, module_name) {
    var isValidSelection = false;
    var CurrentPost = wp.data.select("core/editor").getCurrentPostId();
    jQuery.ajax({
        type: 'POST',
        url: my_ajax_object.ajax_url,
        async: false,
        data: {'action': 'bcp_check_module', 'module_name': module_name, 'page_type': page_type, 'PostId': CurrentPost},
        dataType: 'json',
        success: function (response) {
            isValidSelection = response.page_exists;
        }
    });
    return isValidSelection;
}

let wasSavingPost = wp.data.select('core/editor').isSavingPost();
let wasAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
let wasPreviewingPost = wp.data.select('core/editor').isPreviewingPost();

subscribe(() => {

    const isSavingPost = wp.data.select('core/editor').isSavingPost();
    const isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
    const isPreviewingPost = wp.data.select('core/editor').isPreviewingPost();
    const hasActiveMetaBoxes = wp.data.select('core/edit-post').hasMetaBoxes();

    const TriggerPageSave = ((wasSavingPost && !isSavingPost && !wasAutosavingPost) || (wasAutosavingPost && wasPreviewingPost && !isPreviewingPost));

    wasSavingPost = isSavingPost;
    wasAutosavingPost = isAutosavingPost;
    wasPreviewingPost = isPreviewingPost;

    if (TriggerPageSave) {
        var CurrentPost = wp.data.select("core/editor").getCurrentPostId()
        jQuery.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            async: false,
            data: {action: 'bcp_update_option', 'PostId': CurrentPost},
            dataType: 'json',
            success: function (response) {

            }
        });
    }
});
