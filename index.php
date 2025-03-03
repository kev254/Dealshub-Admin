<?php
switch (@parse_url($_SERVER["REQUEST_URI"])["path"]) {
    case "/auth":
        require "auth.php";
        break;
    case "/my_offers":
        require "my_offers.php";
        break;
    case "/create_offer":
        require "create_offer.php";
        break;
    case "/edit_offer":
        require "edit_offer.php";
        break;
    case "/categories":
        require "my_categories.php";
        break;
    case "/create_category":
        require "create_category.php";
        break;
    case "/edit_category":
        require "edit_category.php";
        break;
    case "/sub_categories":
        require "my_subcategories.php";
        break;
    case "/create_subcategory":
        require "create_subcategory.php";
        break;
    case "/edit_subcategory":
        require "edit_subcategory.php";
        break;

    case "/my_coupons":
        require "my_coupons.php";
        break;
    case "/create_coupon":
        require "create_coupon.php";
        break;
    case "/edit_coupon":
        require "edit_coupon.php";
        break;
    case "/my_products":
        require "my_products.php";
        break;
    case "/create_product":
        require "create_product.php";
        break;
    case "/edit_product":
        require "edit_product.php";
        break;
    case "/my_vendor":
        require "my_vendor.php";
        break;
    case "/branches":
        require "branches.php";
        break;
    case "/create_branch":
        require "create_branch.php";
        break;
    case "/edit_branch":
        require "edit_branch.php";
        break;
    case "/my_reports":
        require "my_reports.php";
        break;

    case "/settings":
        require "settings.php";
        break;
    case "/support_ticket":
        require "support_ticket.php";
        break;
    //admin routes
    case "/admn":
        require "admn_home.php";
        break;
    case "/admn_products":
        require "admn_products.php";
        break;
    case "/admn_offers":
        require "admn_offers.php";
        break;
    case "/admn_coupons":
        require "admn_coupons.php";
        break;
    case "/vendors":
        require "vendors.php";
        break;
    case "/admn_tickets":
        require "admn_tickets.php";
        break;
    case "/admn_sub_categories":
        require "admn_subcategories.php";
        break;
    case "/admn_categories":
        require "admn_categories.php";
        break;
    case "/admn_edit_offer":
        require "admn_edit_offer.php";
        break;

    case "/admn_edit_product":
        require "admn_edit_product.php";
        break;
    case "/admn_edit_coupon":
        require "admn_edit_coupon.php";
        break;
    case "/logout":
        require "logout.php";
        break;


    case "/admn_settings":
        require "admn_settings.php";
        break;
    case "/admn_edit_category":
        require "admn_edit_category.php";
        break;
    case "/admn_create_category":
        require "admn_create_category.php";
        break;
    case "/admn_create_subcategory":
        require "admn_create_subcategory.php";
        break;
    case "/admn_edit_subcategory":
        require "admn_edit_subcategory.php";
        break;
    case "/offer_details":
        require "offer_details.php";
        break;
    case "/admn_bus_types":
        require "admn_bus_types.php";
        break;
    case "/admn_create_business_type":
        require "admn_create_business_type.php";
        break;
    case "/admn_edit_business_type":
        require "admn_edit_business_type.php";
        break;
    case "/admn_list":
        require "admn_list.php";
        break;
    case "/admn_create_admin":
        require "admn_create_admin.php";
        break;
    case "/admn_edit_admin":
        require "admn_edit_admin.php";
        break;


    case "/offers":
        require "offers.php";
        break;
    case "/resetpass":
        require "resetpass.php";
        break;
    case "/forgot":
        require "forgot.php";
        break;
    case "/":
    default:
        require "auth.php";
}