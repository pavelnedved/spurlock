<?php

/*
    Nomenclature.php

    Michael Robinson [1.25.12]

    Spurlock Museum Collections Search support script

    Include this file into the search to provide access to the nomenclature arrays

    $nomen_categories 			- Array of categories
    $nomen_classifications		- Array of categories => array of classifications that match that category
    $nomen_subclassifications	- Array of classifications => array of subclassifications that match that classification
*/


//simple list of all categories
$nomen_categories = array(
    "All",
    "Built Environment Artifacts",
    "Communication T&E",
    "Communication Artifacts",
    "Distribution & Transportation Artifacts",
    "Furnishings",
    "Materials T&E",
    "Personal Artifacts",
    "Recreational Artifacts",
    "Science & Technology T&E",
    "Unclassifiable Artifacts"
);
//associative array that associates an array of classifications with a category name
$nomen_classifications = array(
    "All" => array(
        "All",
        "Acoustical T&E",
        "Adornment",
        "Advertising Media",
        "Aerospace Transportation T&E",
        "Agricultural T&E",
        "Animal Husbandry T&E",
        "Armaments",
        "Art",
        "Astronomical T&E",
        "Bedding",
        "Biological T&E",
        "Building Components",
        "Ceremonial Artifacts",
        "Chemical T&E",
        "Clothing",
        "Construction T&E",
        "Containers",
        "Data Processing T&E",
        "Documentary Artifact",
        "Drafting T&E",
        "Electrical & Magnetic T&E",
        "Energy Production T&E",
        "Exchange Media",
        "Fiberworking T&E",
        "Fishing & Trapping T&E",
        "Floor Coverings",
        "Food Processing & Preparation T&E",
        "Food Service T&E",
        "Forestry T&E",
        "Furniture",
        "Game Equipment",
        "Geological T&E",
        "Glass, Plastics, & Clayworking T&E",
        "Household Accessories",
        "Land Transportation T&E",
        "Leather, Horn & Shellworking T&E",
        "Lighting Equipment",
        "Maintenance T&E",
        "Masonry & Stoneworking T&E",
        "Mechanical T&E",
        "Medical & Psychological T&E",
        "Merchandising T&E",
        "Metalworking T&E",
        "Meteorological T&E",
        "Mining & Mineral Harvesting T&E",
        "Multiple Use T&E for Materials",
        "Musical T&E",
        "Nuclear Physics T&E",
        "Optical T&E",
        "Painting T&E",
        "Papermaking T&E",
        "Personal Gear",
        "Personal Symbols",
        "Photographic T&E",
        "Printing T&E",
        "Public Entertainment Devices",
        "Rail Transportation Equipment",
        "Recreational Devices",
        "Regulative & Protective T&E",
        "Site Feature",
        "Sound Communication T&E",
        "Sports Equipment",
        "Structure",
        "Surveying & Navigational T&E",
        "Telecommunication T&E",
        "Temperature Control Equipment",
        "Textileworking T&E",
        "Thermal T&E",
        "Timekeeping T&E",
        "Toilet Articles",
        "Toys",
        "Visual Communication T&E",
        "Water Transportation Equipment",
        "Weights & Measurements T&E",
        "Window & Door Coverings",
        "Woodworking T&E",
        "Written Communication T&E"
    ),
    "Built Environment Artifacts" => array(
        "All",
        "Building Components",
        "Site Feature",
        "Structure"
    ),
    "Communication T&E" => array(
        "All",
        "Data Processing T&E",
        "Drafting T&E",
        "Musical T&E",
        "Photographic T&E",
        "Printing T&E",
        "Sound Communication T&E",
        "Telecommunication T&E",
        "Visual Communication T&E",
        "Written Communication T&E"
    ),
    "Communication Artifacts" => array(
        "All",
        "Advertising Media",
        "Art",
        "Ceremonial Artifacts",
        "Documentary Artifact",
        "Exchange Media",
        "Personal Symbols"
    ),
    "Distribution & Transportation Artifacts" => array(
        "All",
        "Aerospace Transportation T&E",
        "Containers",
        "Land Transportation T&E",
        "Rail Transportation Equipment",
        "Water Transportation Equipment"
    ),
    "Furnishings" => array(
        "All",
        "Bedding",
        "Floor Coverings",
        "Furniture",
        "Household Accessories",
        "Lighting Equipment",
        "Temperature Control Equipment",
        "Window & Door Coverings"
    ),
    "Materials T&E" => array(
        "All",
        "Agricultural T&E",
        "Animal Husbandry T&E",
        "Fiberworking T&E",
        "Fishing & Trapping T&E",
        "Food Processing & Preparation T&E",
        "Food Service T&E",
        "Forestry T&E",
        "Glass, Plastics, & Clayworking T&E",
        "Leather, Horn & Shellworking T&E",
        "Masonry & Stoneworking T&E",
        "Metalworking T&E",
        "Mining & Mineral Harvesting T&E",
        "Multiple Use T&E for Materials",
        "Painting T&E",
        "Papermaking T&E",
        "Textileworking T&E",
        "Woodworking T&E"
    ),
    "Personal Artifacts" => array(
        "All",
        "Adornment",
        "Clothing",
        "Personal Gear",
        "Toilet Articles"
    ),
    "Recreational Artifacts" => array(
        "All",
        "Game Equipment",
        "Public Entertainment Devices",
        "Recreational Devices",
        "Sports Equipment",
        "Toys"
    ),
    "Science & Technology T&E" => array(
        "All",
        "Acoustical T&E",
        "Armaments",
        "Astronomical T&E",
        "Biological T&E",
        "Chemical T&E",
        "Construction T&E",
        "Electrical & Magnetic T&E",
        "Energy Production T&E",
        "Geological T&E",
        "Maintenance T&E",
        "Mechanical T&E",
        "Medical & Psychological T&E",
        "Merchandising T&E",
        "Meteorological T&E",
        "Nuclear Physics T&E",
        "Optical T&E",
        "Regulative & Protective T&E",
        "Surveying & Navigational T&E",
        "Thermal T&E",
        "Timekeeping T&E",
        "Weights & Measurements T&E"
    ),
    "Unclassifiable Artifacts" => array(
        "All"
    )
);
//associative array that associates an array of subclassifications with a classification
$nomen_subclassifications = array(
    "All" => array(
        "All",
        "Achievement Symbols",
        "Administrative Records",
        "Aerospace Transportation Accessories",
        "Agricultural Spaces",
        "Aircraft",
        "Ammunition",
        "Animal Care Equipment",
        "Animal-Powered Vehicles",
        "Architectural Spaces",
        "Armament Accessories",
        "Artillery",
        "Barrier Elements",
        "Beauty Supplies",
        "Belief Symbols",
        "Body Adornments",
        "Body Armor",
        "Bookbinding Equipment",
        "Breeding Equipment",
        "Camera Equipment",
        "Ceremonial Structures",
        "Chemical Testing Devices",
        "Civic & Social Structures",
        "Clothing Accessories",
        "Clothing Care Artifacts",
        "Commercial Structures",
        "Construction Elements",
        "Containers for Smoking & Tobacco",
        "Conveyance Devices",
        "Cooking Vessels",
        "Cultivation Equipment",
        "Cultural & Recreational Structures",
        "Data Processing Accessories",
        "Data Processing Devices",
        "Declaratory Documents",
        "Decorative Furnishings",
        "Defense Structures",
        "Dental Accessories",
        "Dental Instruments",
        "Dishwashing Equipment",
        "Door & Window Coverings",
        "Dressingwear & Nightwear",
        "Drinking Vessels",
        "Dwellings",
        "Eating & Drinking Utensils",
        "Eating Vessels",
        "Edged Weapons",
        "Electrical & Magnetic Measurement Devices",
        "Electrical Maintenance & Repair Equipment",
        "Electrical System Components",
        "Environmental Control Elements",
        "Farrier Equipment",
        "Feed Processing Equipment",
        "Financial Records",
        "Finish Hardware",
        "Firearms",
        "Firemaking Equipment",
        "Fishing Equipment",
        "Food Preparation Accessories",
        "Food Preparation Equipment",
        "Food Processing Equipment",
        "Food Service Accessories",
        "Food Service Sets",
        "Food Storage Equipment",
        "Footwear",
        "Funerary Objects",
        "Furniture Coverings",
        "Furniture Sets",
        "Government Records",
        "Graphic Documents",
        "Graphic Equipment",
        "Groundskeeping Equipment",
        "Hair Adornments",
        "Hair Care Artifacts",
        "Harvesting Equipment",
        "Headwear",
        "Heating & Cooling Equipment",
        "Heating Equipment Accessories",
        "Holiday Objects",
        "Horticultural Containers",
        "Housekeeping Equipment",
        "Human-Powered Vehicles",
        "Hydraulic Structures",
        "Hygiene Artifacts",
        "Industrial Structures",
        "Institutional Structures",
        "Instructional Documents",
        "Labware",
        "Land Transportation Accessories",
        "Laundry Equipment",
        "Legal Documents",
        "Lighting Devices",
        "Lighting Holders",
        "Literary Works",
        "Main Garments",
        "Mechanical Devices",
        "Mechanical Measurement Equipment",
        "Medical Accessories",
        "Medical Instruments",
        "Memorabilia",
        "Motor Vehicles",
        "Musical Accessories",
        "Musical Instruments",
        "Navigational Equipment",
        "Needleworking T&E",
        "Organizational Objects",
        "Other Documents",
        "Other Energy Production T&E",
        "Other Furniture",
        "Other Household Accessories",
        "Other Lighting Accessories",
        "Other Structures",
        "Outbuildings",
        "Outerwear",
        "Party Accessories",
        "Percussive Weapons",
        "Peripherals",
        "Personal Assistive Artifacts",
        "Personal Carrying & Storage Gear",
        "Personal Indentification",
        "Pet Supplies",
        "Photographic Accessories",
        "Photographic Media",
        "Photoprocessing Equipment",
        "Planting Equipment",
        "Plumbing & Drainage Elements",
        "Power Producing Equipment",
        "Power Transmission Components",
        "Printing Accessories",
        "Protective Devices",
        "Protective Wear",
        "Rail Transportation Accessories",
        "Rail Vehicles",
        "Regulative Devices",
        "Religious Objects",
        "Replication Equipment",
        "Roof Elements",
        "Seating Furniture",
        "Serving Utensils",
        "Serving Vessels",
        "Sleeping & Reclining Furniture",
        "Smoking & Recreational Drug Equipment",
        "Sound Communication Accessories",
        "Sound Communication Devices",
        "Sound Communication Media",
        "Spacecraft",
        "Stair Elements",
        "Status Symbols",
        "Storage & Display Accessories",
        "Storage & Display Furniture",
        "Support Furniture",
        "Supporting Elements",
        "Surface Elements",
        "Surveying Equipment",
        "Telecommunication Accessories",
        "Telecommunication Devices",
        "Telecommunication Media",
        "Tending Equipment",
        "Textile Manufacturing Equipment",
        "Transportation Structures",
        "Trapping Equipment",
        "Typesetting Equipment",
        "Underwear",
        "Veterinary Equipment",
        "Visual Communication Accessories",
        "Visual Communication Devices",
        "Water Transportation Accessories",
        "Watercraft",
        "Wedding Objects",
        "Writing Accessories",
        "Writing Devices",
        "Writing Media"
    ),
    "Building Components" => array(
        "All",
        "Architectural Spaces",
        "Barrier Elements",
        "Construction Elements",
        "Conveyance Devices",
        "Door & Window Coverings",
        "Environmental Control Elements",
        "Finish Hardware",
        "Plumbing & Drainage Elements",
        "Roof Elements",
        "Supporting Elements",
        "Stair Elements",
        "Surface Elements"
    ),
    "Site Feature" => array(
        "All"
    ),
    "Structure" => array(
        "All",
        "Agricultural Spaces",
        "Ceremonial Structures",
        "Civic & Social Structures",
        "Commercial Structures",
        "Cultural & Recreational Structures",
        "Defense Structures",
        "Dwellings",
        "Hydraulic Structures",
        "Industrial Structures",
        "Institutional Structures",
        "Outbuildings",
        "Transportation Structures",
        "Other Structures"
    ),
    "Data Processing T&E" => array(
        "All",
        "Data Processing Accessories",
        "Data Processing Devices",
        "Peripherals"
    ),
    "Drafting T&E" => array(
        "All"
    ),
    "Musical T&E" => array(
        "All",
        "Musical Accessories",
        "Musical Instruments"
    ),
    "Photographic T&E" => array(
        "All",
        "Camera Equipment",
        "Photographic Accessories",
        "Photographic Media",
        "Photoprocessing Equipment"
    ),
    "Printing T&E" => array(
        "All",
        "Bookbinding Equipment",
        "Graphic Equipment",
        "Printing Accessories",
        "Replication Equipment",
        "Typesetting Equipment"
    ),
    "Sound Communication T&E" => array(
        "All",
        "Sound Communication Accessories",
        "Sound Communication Devices",
        "Sound Communication Media"
    ),
    "Telecommunication T&E" => array(
        "All",
        "Telecommunication Accessories",
        "Telecommunication Devices",
        "Telecommunication Media"
    ),
    "Visual Communication T&E" => array(
        "All",
        "Visual Communication Accessories",
        "Visual Communication Devices",
    ),
    "Written Communication T&E" => array(
        "All",
        "Writing Accessories",
        "Writing Devices",
        "Writing Media"
    ),
    "Advertising Media" => array(
        "All"
    ),
    "Art" => array(
        "All"
    ),
    "Ceremonial Artifacts" => array(
        "All",
        "Funerary Objects",
        "Holiday Objects",
        "Organizational Objects",
        "Party Accessories",
        "Religious Objects",
        "Wedding Objects"
    ),
    "Documentary Artifact" => array(
        "All",
        "Administrative Records",
        "Declaratory Documents",
        "Financial Records",
        "Government Records",
        "Graphic Documents",
        "Instructional Documents",
        "Legal Documents",
        "Literary Works",
        "Memorabilia",
        "Other Documents"
    ),
    "Exchange Media" => array(
        "All"
    ),
    "Personal Symbols" => array(
        "All",
        "Achievement Symbols",
        "Belief Symbols",
        "Personal Indentification",
        "Status Symbols"
    ),
    "Aerospace Transportation T&E" => array(
        "All",
        "Aerospace Transportation Accessories",
        "Aircraft",
        "Spacecraft"
    ),
    "Containers" => array(
        "All"
    ),
    "Land Transportation T&E" => array(
        "All",
        "Land Transportation Accessories",
        "Animal-Powered Vehicles",
        "Human-Powered Vehicles",
        "Motor Vehicles"
    ),
    "Rail Transportation Equipment" => array(
        "All",
        "Rail Transportation Accessories",
        "Rail Vehicles"
    ),
    "Water Transportation Equipment" => array(
        "All",
        "Water Transportation Accessories",
        "Watercraft"
    ),
    "Bedding" => array(
        "All"
    ),
    "Floor Coverings" => array(
        "All"
    ),
    "Furniture" => array(
        "All",
        "Furniture Sets",
        "Seating Furniture",
        "Sleeping & Reclining Furniture",
        "Storage & Display Furniture",
        "Support Furniture",
        "Other Furniture"
    ),
    "Household Accessories" => array(
        "All",
        "Containers for Smoking & Tobacco",
        "Decorative Furnishings",
        "Furniture Coverings",
        "Horticultural Containers",
        "Storage & Display Accessories",
        "Other Household Accessories"
    ),
    "Lighting Equipment" => array(
        "All",
        "Lighting Devices",
        "Lighting Holders",
        "Other Lighting Accessories"
    ),
    "Temperature Control Equipment" => array(
        "All",
        "Firemaking Equipment",
        "Heating & Cooling Equipment",
        "Heating Equipment Accessories"
    ),
    "Window & Door Coverings" => array(
        "All"
    ),
    "Agricultural T&E" => array(
        "All",
        "Cultivation Equipment",
        "Feed Processing Equipment",
        "Harvesting Equipment",
        "Planting Equipment",
        "Tending Equipment"
    ),
    "Animal Husbandry T&E" => array(
        "All",
        "Animal Care Equipment",
        "Breeding Equipment",
        "Farrier Equipment",
        "Pet Supplies",
        "Veterinary Equipment"
    ),
    "Fiberworking T&E" => array(
        "All"
    ),
    "Fishing & Trapping T&E" => array(
        "All",
        "Fishing Equipment",
        "Trapping Equipment"
    ),
    "Food Processing & Preparation T&E" => array(
        "All",
        "Cooking Vessels",
        "Food Preparation Accessories",
        "Food Preparation Equipment",
        "Food Processing Equipment",
        "Food Storage Equipment"
    ),
    "Food Service T&E" => array(
        "All",
        "Drinking Vessels",
        "Eating Vessels",
        "Eating & Drinking Utensils",
        "Food Service Accessories",
        "Food Service Sets",
        "Serving Utensils",
        "Serving Vessels"
    ),
    "Forestry T&E" => array(
        "All"
    ),
    "Glass, Plastics, & Clayworking T&E" => array(
        "All"
    ),
    "Leather, Horn & Shellworking T&E" => array(
        "All"
    ),
    "Masonry & Stoneworking T&E" => array(
        "All"
    ),
    "Metalworking T&E" => array(
        "All"
    ),
    "Mining & Mineral Harvesting T&E" => array(
        "All"
    ),
    "Multiple Use T&E for Materials" => array(
        "All"
    ),
    "Painting T&E" => array(
        "All"
    ),
    "Papermaking T&E" => array(
        "All"
    ),
    "Textileworking T&E" => array(
        "All",
        "Needleworking T&E",
        "Textile Manufacturing Equipment"
    ),
    "Woodworking T&E" => array(
        "All"
    ),
    "Adornment" => array(
        "All",
        "Body Adornments",
        "Hair Adornments"
    ),
    "Clothing" => array(
        "All",
        "Clothing Accessories",
        "Dressingwear & Nightwear",
        "Footwear",
        "Headwear",
        "Main Garments",
        "Outerwear",
        "Protective Wear",
        "Underwear"
    ),
    "Personal Gear" => array(
        "All",
        "Clothing Care Artifacts",
        "Personal Assistive Artifacts",
        "Personal Carrying & Storage Gear",
        "Smoking & Recreational Drug Equipment"
    ),
    "Toilet Articles" => array(
        "All",
        "Beauty Supplies",
        "Hair Care Artifacts",
        "Hygiene Artifacts"
    ),
    "Game Equipment" => array(
        "All"
    ),
    "Public Entertainment Devices" => array(
        "All"
    ),
    "Recreational Devices" => array(
        "All"
    ),
    "Sports Equipment" => array(
        "All"
    ),
    "Toys" => array(
        "All"
    ),
    "Acoustical T&E" => array(
        "All"
    ),
    "Armaments" => array(
        "All",
        "Ammunition",
        "Armament Accessories",
        "Artillery",
        "Body Armor",
        "Edged Weapons",
        "Firearms",
        "Percussive Weapons"
    ),
    "Astronomical T&E" => array(
        "All"
    ),
    "Biological T&E" => array(
        "All"
    ),
    "Chemical T&E" => array(
        "All",
        "Chemical Testing Devices",
        "Labware"
    ),
    "Construction T&E" => array(
        "All"
    ),
    "Electrical & Magnetic T&E" => array(
        "All",
        "Electrical & Magnetic Measurement Devices",
        "Electrical Maintenance & Repair Equipment",
        "Electrical System Components"
    ),
    "Energy Production T&E" => array(
        "All",
        "Power Producing Equipment",
        "Power Transmission Components",
        "Other Energy Production T&E"
    ),
    "Geological T&E" => array(
        "All"
    ),
    "Maintenance T&E" => array(
        "All",
        "Dishwashing Equipment",
        "Groundskeeping Equipment",
        "Housekeeping Equipment",
        "Laundry Equipment"
    ),
    "Mechanical T&E" => array(
        "All",
        "Mechanical Devices",
        "Mechanical Measurement Equipment"
    ),
    "Medical & Psychological T&E" => array(
        "All",
        "Dental Accessories",
        "Dental Instruments",
        "Medical Accessories",
        "Medical Instruments"
    ),
    "Merchandising T&E" => array(
        "All"
    ),
    "Meteorological T&E" => array(
        "All"
    ),
    "Nuclear Physics T&E" => array(
        "All"
    ),
    "Optical T&E" => array(
        "All"
    ),
    "Regulative & Protective T&E" => array(
        "All",
        "Protective Devices",
        "Regulative Devices"
    ),
    "Surveying & Navigational T&E" => array(
        "All",
        "Navigational Equipment",
        "Surveying Equipment"
    ),
    "Thermal T&E" => array(
        "All"
    ),
    "Timekeeping T&E" => array(
        "All"
    ),
    "Weights & Measurements T&E" => array(
        "All"
    ),
);
//associates subclasses with categories
$nomen_categories_subclasses = array(
    "All" => array(
        "All",
        "Achievement Symbols",
        "Administrative Records",
        "Aerospace Transportation Accessories",
        "Agricultural Spaces",
        "Aircraft",
        "Ammunition",
        "Animal Care Equipment",
        "Animal-Powered Vehicles",
        "Architectural Spaces",
        "Armament Accessories",
        "Artillery",
        "Barrier Elements",
        "Beauty Supplies",
        "Belief Symbols",
        "Body Adornments",
        "Body Armor",
        "Bookbinding Equipment",
        "Breeding Equipment",
        "Camera Equipment",
        "Ceremonial Structures",
        "Chemical Testing Devices",
        "Civic & Social Structures",
        "Clothing Accessories",
        "Clothing Care Artifacts",
        "Commercial Structures",
        "Construction Elements",
        "Containers for Smoking & Tobacco",
        "Conveyance Devices",
        "Cooking Vessels",
        "Cultivation Equipment",
        "Cultural & Recreational Structures",
        "Data Processing Accessories",
        "Data Processing Devices",
        "Declaratory Documents",
        "Decorative Furnishings",
        "Defense Structures",
        "Dental Accessories",
        "Dental Instruments",
        "Dishwashing Equipment",
        "Door & Window Coverings",
        "Dressingwear & Nightwear",
        "Drinking Vessels",
        "Dwellings",
        "Eating & Drinking Utensils",
        "Eating Vessels",
        "Edged Weapons",
        "Electrical & Magnetic Measurement Devices",
        "Electrical Maintenance & Repair Equipment",
        "Electrical System Components",
        "Environmental Control Elements",
        "Farrier Equipment",
        "Feed Processing Equipment",
        "Financial Records",
        "Finish Hardware",
        "Firearms",
        "Firemaking Equipment",
        "Fishing Equipment",
        "Food Preparation Accessories",
        "Food Preparation Equipment",
        "Food Processing Equipment",
        "Food Service Accessories",
        "Food Service Sets",
        "Food Storage Equipment",
        "Footwear",
        "Funerary Objects",
        "Furniture Coverings",
        "Furniture Sets",
        "Government Records",
        "Graphic Documents",
        "Graphic Equipment",
        "Groundskeeping Equipment",
        "Hair Adornments",
        "Hair Care Artifacts",
        "Harvesting Equipment",
        "Headwear",
        "Heating & Cooling Equipment",
        "Heating Equipment Accessories",
        "Holiday Objects",
        "Horticultural Containers",
        "Housekeeping Equipment",
        "Human-Powered Vehicles",
        "Hydraulic Structures",
        "Hygiene Artifacts",
        "Industrial Structures",
        "Institutional Structures",
        "Instructional Documents",
        "Labware",
        "Land Transportation Accessories",
        "Laundry Equipment",
        "Legal Documents",
        "Lighting Devices",
        "Lighting Holders",
        "Literary Works",
        "Main Garments",
        "Mechanical Devices",
        "Mechanical Measurement Equipment",
        "Medical Accessories",
        "Medical Instruments",
        "Memorabilia",
        "Motor Vehicles",
        "Musical Accessories",
        "Musical Instruments",
        "Navigational Equipment",
        "Needleworking T&E",
        "Organizational Objects",
        "Other Documents",
        "Other Energy Production T&E",
        "Other Furniture",
        "Other Household Accessories",
        "Other Lighting Accessories",
        "Other Structures",
        "Outbuildings",
        "Outerwear",
        "Party Accessories",
        "Percussive Weapons",
        "Peripherals",
        "Personal Assistive Artifacts",
        "Personal Carrying & Storage Gear",
        "Personal Indentification",
        "Pet Supplies",
        "Photographic Accessories",
        "Photographic Media",
        "Photoprocessing Equipment",
        "Planting Equipment",
        "Plumbing & Drainage Elements",
        "Power Producing Equipment",
        "Power Transmission Components",
        "Printing Accessories",
        "Protective Devices",
        "Protective Wear",
        "Rail Transportation Accessories",
        "Rail Vehicles",
        "Regulative Devices",
        "Religious Objects",
        "Replication Equipment",
        "Roof Elements",
        "Seating Furniture",
        "Serving Utensils",
        "Serving Vessels",
        "Sleeping & Reclining Furniture",
        "Smoking & Recreational Drug Equipment",
        "Sound Communication Accessories",
        "Sound Communication Devices",
        "Sound Communication Media",
        "Spacecraft",
        "Stair Elements",
        "Status Symbols",
        "Storage & Display Accessories",
        "Storage & Display Furniture",
        "Support Furniture",
        "Supporting Elements",
        "Surface Elements",
        "Surveying Equipment",
        "Telecommunication Accessories",
        "Telecommunication Devices",
        "Telecommunication Media",
        "Tending Equipment",
        "Textile Manufacturing Equipment",
        "Transportation Structures",
        "Trapping Equipment",
        "Typesetting Equipment",
        "Underwear",
        "Veterinary Equipment",
        "Visual Communication Accessories",
        "Visual Communication Devices",
        "Water Transportation Accessories",
        "Watercraft",
        "Wedding Objects",
        "Writing Accessories",
        "Writing Devices",
        "Writing Media"
    ),
    "Built Environment Artifacts" => array(
        "All",
        "Agricultural Spaces",
        "Architectural Spaces",
        "Barrier Elements",
        "Ceremonial Structures",
        "Civic & Social Structures",
        "Commercial Structures",
        "Construction Elements",
        "Conveyance Devices",
        "Cultural & Recreational Structures",
        "Defense Structures",
        "Door & Window Coverings",
        "Dwellings",
        "Environmental Control Elements",
        "Finish Hardware",
        "Hydraulic Structures",
        "Industrial Structures",
        "Institutional Structures",
        "Other Structures",
        "Outbuildings",
        "Plumbing & Drainage Elements",
        "Roof Elements",
        "Stair Elements",
        "Supporting Elements",
        "Surface Elements",
        "Transportation Structures"
    ),
    "Communication T&E" => array(
        "All",
        "Bookbinding Equipment",
        "Camera Equipment",
        "Data Processing Accessories",
        "Data Processing Devices",
        "Graphic Equipment",
        "Musical Accessories",
        "Musical Instruments",
        "Peripherals",
        "Photographic Accessories",
        "Photographic Media",
        "Photoprocessing Equipment",
        "Printing Accessories",
        "Replication Equipment",
        "Sound Communication Accessories",
        "Sound Communication Devices",
        "Sound Communication Media",
        "Telecommunication Accessories",
        "Telecommunication Devices",
        "Telecommunication Media",
        "Typesetting Equipment",
        "Visual Communication Accessories",
        "Visual Communication Devices",
        "Writing Accessories",
        "Writing Devices",
        "Writing Media"
    ),
    "Communication Artifacts" => array(
        "All",
        "Achievement Symbols",
        "Administrative Records",
        "Belief Symbols",
        "Declaratory Documents",
        "Financial Records",
        "Funerary Objects",
        "Government Records",
        "Graphic Documents",
        "Holiday Objects",
        "Instructional Documents",
        "Legal Documents",
        "Literary Works",
        "Memorabilia",
        "Organizational Objects",
        "Other Documents",
        "Party Accessories",
        "Personal Identification",
        "Religious Objects",
        "Status Symbols",
        "Wedding Objects"
    ),
    "Distribution & Transportation Artifacts" => array(
        "All",
        "Aerospace Transportation Accessories",
        "Aircraft",
        "Animal-Powered Vehicles",
        "Human-Powered Vehicles",
        "Land Transportation Accessories",
        "Motor Vehicles",
        "Rail Transportation Accessories",
        "Rail Vehicles",
        "Spacecraft",
        "Water Transportation Accessories",
        "Watercraft"
    ),
    "Furnishings" => array(
        "All",
        "Containers for Smoking & Tobacco",
        "Decorative Furnishings",
        "Firemaking Equipment",
        "Furniture Coverings",
        "Furniture Sets",
        "Heating & Cooling Equipment",
        "Heating Equipment Accessories",
        "Horticultural Containers",
        "Lighting Devices",
        "Lighting Holders",
        "Other Furniture",
        "Other Household Accessories",
        "Other Lighting Accessories",
        "Seating Furniture",
        "Sleeping & Reclining Furniture",
        "Storage & Display Accessories",
        "Storage & Display Furniture",
        "Support Furniture"
    ),
    "Materials T&E" => array(
        "All",
        "Animal Care Equipment",
        "Breeding Equipment",
        "Cooking Vessels",
        "Cultivation Equipment",
        "Drinking Vessels",
        "Eating & Drinking Utensils",
        "Eating Vessels",
        "Farrier Equipment",
        "Feed Processing Equipment",
        "Fishing Equipment",
        "Food Preparation Accessories",
        "Food Preparation Equipment",
        "Food Processing Equipment",
        "Food Service Accessories",
        "Food Service Sets",
        "Food Storage Equipment",
        "Harvesting Equipment",
        "Needleworking T&E",
        "Pet Supplies",
        "Planting Equipment",
        "Serving Utensils",
        "Serving Vessels",
        "Tending Equipment",
        "Textile Manufacturing Equipment",
        "Trapping Equipment",
        "Veterinary Equipment"
    ),
    "Personal Artifacts" => array(
        "All",
        "Beauty Supplies",
        "Body Adornments",
        "Clothing Accessories",
        "Clothing Care Artifacts",
        "Dressingwear & Nightwear",
        "Footwear",
        "Hair Adornments",
        "Hair Care Artifacts",
        "Headwear",
        "Hygiene Artifacts",
        "Main Garments",
        "Outerwear",
        "Personal Assistive Artifacts",
        "Personal Carrying & Storage Gear",
        "Protective Wear",
        "Smoking & Recreational Drug Equipment",
        "Underwear"
    ),
    "Recreational Artifacts" => array(
        "All"
    ),
    "Science & Technology T&E" => array(
        "All",
        "Ammunition",
        "Armament Accessories",
        "Artillery",
        "Body Armor",
        "Chemical Testing Devices",
        "Dental Accessories",
        "Dental Instruments",
        "Dishwashing Equipment",
        "Edged Weapons",
        "Electrical & Magnetic Measurement Devices",
        "Electrical Maintenance & Repair Equipment",
        "Electrical System Components",
        "Firearms",
        "Groundskeeping Equipment",
        "Housekeeping Equipment",
        "Labware",
        "Laundry Equipment",
        "Mechanical Devices",
        "Mechanical Measurement Equipment",
        "Medical Accessories",
        "Medical Instruments",
        "Navigational Equipment",
        "Other Energy Production T&E",
        "Percussive Weapons",
        "Power Producing Equipment",
        "Power Transmission Components",
        "Protective Devices",
        "Regulative Devices",
        "Surveying Equipment"
    ),
    "Unclassifiable Artifacts" => array(
        "All"
    )
);

?>