=== Piwi Warehouse ===
Contributors: roccomarco
Tags: warehouse, management system
Requires at least: 5.0
Tested up to: 6.3.1
Requires PHP: 7.2
Stable tag: 3.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Piwi Warehouse is a simple warehouse management system that allows you to keep track of your items, their location and record loan and restitutions.

== Description ==

Piwi Warehouse is a simple warehouse management system that allows you to keep track of your items, their location and record loan and restitutions.

Piwi Warehouse introduces the following elements:

 * Items - the element you want to keep track of.
 * Types - a hierarchical taxonomy to organize your Items.
 * Locations - the locations of your Warehouse where the item will be stored (e.g. "Facility A01", "Lab 1", "Cabinet B03", "Box B22").
 * Purchases - an operation that increases the Item total amount and availability in a specific Location
 * Holders - an entity that can loan Items
 * Movements - an operation that assigns a specific amount of Items to an Holder. The Movement keep an history log of all the operations.

== Screenshots ==

1. Overview of the main Warehouse menu
2. An example of a Movement
3. Screenshot of the Capability Engine

== FAQ ==

= How to notify a Bug =
The development repository and the bug ticketing system of this plugin is hosted on Sourceforge. If you find a bug submit it at this url:
[Warehouse Bug Tracer](https://sourceforge.net/p/piwi-lib/bugs-warehouse/ "Warehouse Bug Tracer")

== Changelog ==

= 3.1.3 =
 * NEW: Updated scripts version.
 
= 3.1.2 =
 * NEW: Minor fixes and improvements in the JS handling.
        
= 3.1.1 =
 * BUG: Fixed wrong label for the movement publish button (bug #27).
 * BUG: Fixed Wrong locations list when a draft item with a duplicated name is
        saved (bug #26).

= 3.1.0 =
 * NEW: Purchases are now tied to the locations: when purchasing an item, it is
        required to assign a destination. Item locations are automatically
        assigned on a Purchase. Validation mechanism have been changed.
 * NEW: Extended Item Record postbox to handle the availability per location.
 * NEW: Item availabilities are now tied to location. Aside of the total
        availability, each item has an availability per location.
 * NEW: Implemented a new notes system for Purchase an Movement
 * NEW: Improved Post Submit Box management.

= 3.0.1 =
 * BUG: Fixed issue with capability page restore action (bug #25).

= 3.0.0 =
 * NEW: Reorganized the entire hierarchy introducing a better separation in
       the various part of the system
 * NEW: Introduced a new engine to manage the back-end pages

= 2.0.2 =
 * NEW: Added missing messages to custom post types.
 * NEW: Minor fixes.
 * NEW: Movements' item box now shows shelfmark info.
 * NEW: Improved sanitization of joiner.
 * NEW: Updated Piwi Library to 1.2.2.

= 2.0.1 =
 * NEW: Improved sanitization.
 * NEW: Updated Piwi Library to 1.2.1.
 * BUG: Fixed issue with item titles (bug #24).
 * BUG: Fixed issue which prevent deletion of first movement item (bug #23).
 * BUG: Fixed misuse of a variable in Item Type column (bug #22).
 * BUG: Fixed vrong aphostrope management in Item title (bug #21).
 * BUG: Fixed a check in History insert to ensure an error log when Holder ID
       is NULL (bug #20).
 * BUG: Fixed issue in History insert when movement transitate from active to
       something else (bug #19).

= 2.0.0 =
 * NEW: Updated Piwi Library to 1.2.0.
 * NEW: Implemented Consistency Checker.
 * NEW: Implemented Movement Join Tool.
 * NEW: Implemented Multi-item Movement.
 * NEW: Implemented Multi-item Balance.
 * NEW: Implemented Multi-item Purchase.
 * NEW: Improved Quick Operations box.
 * NEW: Improved capability engine: added Create Item, Create Balance and Create
        Purchase, Create Movement, Update Movement capabilities.

= 1.1.1 =
 * BUG: Fixed unexpected behavior of date change block (bug #15).
 * BUG: Fixed post date button style issue (bug #14).
 * BUG: Fixed wrong sorting of Balance, Purchase and Movement title column of
        Post List (bug #13).
 * BUG: Fixed error in Shelfmark/Type column of Item List (bug #12).

= 1.1.0 =
 * NEW: Updated PLib to 1.1.0.
 * NEW: Added update method in movement history class.
 * NEW: Added capability management page.
 * NEW: Added capability engine.
 * NEW: Shelfmarks and Types now are organized as linked list in list table.
 * NEW: Improved movement interface: now is more responsive.
 * NEW: Item is now click-able in infoboxes.
 * NEW: Added Settings page.
 * NEW: Improved admin sub-menu order.

= 1.0.2 =
 * BUG: Fixed wrong sorting of Balance, Purchase and Movement title column of
       Post List (bug #13).

= 1.0.1 =
 * NEW: Updated PLib to 1.0.1.
 * NEW: Added Item related css for minor style improvements.
 * BUG: Fixed unescaped attribute in prefilled item metabox (bug #10).
 * BUG: Improved update lent js (bug #9).
 * BUG: Fixed Conclude button not properly internationalized (bug #8).
 * BUG: Fixed extra div when no featured is available in info box (bug #7).
 * BUG: Fixed misleading submit div title (bug #6).
 * BUG: Fixed purchase quantity error message (bug #5).
 * BUG: Fixed item datalist limit (bug #4).
 * BUG: Fixed wrong initialization of Item amount and availability (bug #3).
 * BUG: Fixed wrong translation (bug #2).
 * BUG: Fixed wrong JS inclusion and removed old files (bug #1).

= 1.0 =
 * Initial release.
