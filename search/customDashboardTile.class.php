<?php

require_once("dashboardTile.class.php");

/**
 * Class customDashboardTile
 *
 * Used to display special content dashboard tiles. To use, instantiate an object of this class, passing your custom
 * display function into the constructor. That function will be called when the client wants to display the tile. See
 * dashboardTileFactory.class.php for an example.
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class customDashboardTile extends dashboardTile
{

    /**
     * @var function
     */
    private $customDisplayFunction = null;

    public function __construct($customDisplayFunction)
    {
        parent::__construct(array(), '', '', TRUE);
        $this->customDisplayFunction = $customDisplayFunction;
    }

    /**
     * Displays the tile or returns the HTML it would have displayed as a string
     * @param bool $echo Boolean determining whether it should echo the HTML or return it as a string
     * @return string The HTML it would have echoed
     */
    public function display($echo = true)
    {
        if ($this->customDisplayFunction != null) {
            return call_user_func($this->customDisplayFunction, $echo);
        }
    }
}


?>