<?php

//user roles
$aUserRoles = array(
                    1=>'manager',
                    2=>'sales',
                    3=>'security',
                    4=>'cashier',
                    5=>'driver'
                   );

//mesure systems
$aMeasureTypes = array(
                         1=>'Weight (grams)',
                         2=>'QTY'
                      );

$aWeights = array(
                    1=>array('name'=>'Gram','quantity'=>1)
                 );

$aQTY = array(
                1=>array('name'=>'qty','quantity'=>1)
             );

$aAlternativeWeights = array(
    'pre_roll' => array('name'=>'Pre Roll','quantity'=>1),
    'halfeighth' => array('name'=>'1/2 8th','quantity'=>1.75),
    'twograms'  => array('name'=>'2 grams','quantity'=>2),
    'eighth' => array('name'=>'8th','quantity'=>3.5),
    'fourgrams' => array('name'=>'4 grams (8th)','quantity'=>4),
    'fivegrams' => array('name'=>'5 grams','quantity'=>5),
    'fourth' => array('name'=>'1/4','quantity'=>7),
    'half'=> array('name'=>'1/2','quantity'=>14),
    'oz'=> array('name'=>'Oz','quantity'=>28)    
);

$aDays = array(1=>'Mon', 2=>'Tue', 3=>'Wed', 4=>'Thu', 5=>'Fri', 6=>'Sat', 0=>'Sun');

$aStates = array(
    'AL'=>'Alabama',
    'AK'=>'Alaska',
    'AZ'=>'Arizona',
    'AR'=>'Arkansas',
    'CA'=>'California',
    'CO'=>'Colorado',
    'CT'=>'Connecticut',
    'DE'=>'Delaware',
    'DC'=>'District of Columbia',
    'FL'=>'Florida',
    'GA'=>'Georgia',
    'HI'=>'Hawaii',
    'ID'=>'Idaho',
    'IL'=>'Illinois',
    'IN'=>'Indiana',
    'IA'=>'Iowa',
    'KS'=>'Kansas',
    'KY'=>'Kentucky',
    'LA'=>'Louisiana',
    'ME'=>'Maine',
    'MD'=>'Maryland',
    'MA'=>'Massachusetts',
    'MI'=>'Michigan',
    'MN'=>'Minnesota',
    'MS'=>'Mississippi',
    'MO'=>'Missouri',
    'MT'=>'Montana',
    'NE'=>'Nebraska',
    'NV'=>'Nevada',
    'NH'=>'New Hampshire',
    'NJ'=>'New Jersey',
    'NM'=>'New Mexico',
    'NY'=>'New York',
    'NC'=>'North Carolina',
    'ND'=>'North Dakota',
    'OH'=>'Ohio',
    'OK'=>'Oklahoma',
    'OR'=>'Oregon',
    'PA'=>'Pennsylvania',
    'RI'=>'Rhode Island',
    'SC'=>'South Carolina',
    'SD'=>'South Dakota',
    'TN'=>'Tennessee',
    'TX'=>'Texas',
    'UT'=>'Utah',
    'VT'=>'Vermont',
    'VA'=>'Virginia',
    'WA'=>'Washington',
    'WV'=>'West Virginia',
    'WI'=>'Wisconsin',
    'WY'=>'Wyoming',
);

$aThemes = array(
    1=>array('name'=>'White','class'=>''),
    2=>array('name'=>'Green','class'=>'green-table'),
    3=>array('name'=>'Light Green','class'=>'light-green-table'),
);