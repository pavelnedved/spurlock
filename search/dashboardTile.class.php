<?php

/**
 * Class dashboardTile
 *
 * Represents a dashboard tile that can be displayed. Holds the necessary information to display a tile of artifacts
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class dashboardTile
{

    /**
     * @var array
     */
    private $resultSet = array();
    /**
     * @var string
     */
    private $barTitle = "";
    /**
     * @var string
     */
    private $URL = "";
    /**
     * @var bool
     */
    private $shuf = true;
    /**
     * @var string
     */
    private $class = "";

    /**
     * The size of each tile, in px
     */
    const TILE_IMAGE_SIZE = 150;

    public function __construct($resultSet, $title, $class, $URL, $shuffle = true)
    {
        $this->resultSet = $resultSet;
        $this->barTitle = $title;
        $this->shuf = $shuffle;
        $this->URL = $URL;
        $this->class = $class;
    }

    /**
     * Displays the tile or returns the HTML it would have displayed as a string
     * @param bool $echo Boolean determining whether it should echo the HTML or return it as a string
     * @return string The HTML it would have echoed
     */
    public function display($echo = true)
    {
        $output = "";
        if (count($this->resultSet) > 0) {
            if ($this->shuf) {
                shuffle($this->resultSet);
            }
            $totalShown = 0;
            $output .= '<div class="tile ' . $this->class . '"><a href="' . $this->URL . '">';
            if ($this->barTitle != '') {
                $output .= '<div class="tileTitle"><span>' . $this->barTitle . '</span></div>';
            }
            for ($i = 0; $i < count($this->resultSet); $i++) {
                $artifact = $this->resultSet[$i];
                if ($totalShown == count($this->resultSet) - 1) {
                    $output .= '<div class="tileItem lastTileItem"><div class="tileImgCont">';
                } else {
                    $output .= '<div class="tileItem"><div class="tileImgCont">';
                }
                List($width, $height) = getimagesize($artifact->getThumbImage(artifact::TILE_CONTEXT));
                if ($width == $height) {
                    $output .= '		<img src="' . $artifact->getThumbImage(artifact::TILE_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" width="' . self::TILE_IMAGE_SIZE . '">';
                } else if ($width > $height) {
                    $newWidth = self::TILE_IMAGE_SIZE / $height * $width;
                    $output .= '		<img src="' . $artifact->getThumbImage(artifact::TILE_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" height="' . self::TILE_IMAGE_SIZE . '" style="margin-left: -' . ($newWidth - self::TILE_IMAGE_SIZE) / 2 . 'px">';
                } else { //$width < $height
                    $output .= '		<img src="' . $artifact->getThumbImage(artifact::TILE_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" width="' . self::TILE_IMAGE_SIZE . '">';
                }
                $output .= '		</div></div>';
                $totalShown++;
            }
            $output .= '</a></div>';
            if ($echo) {
                echo $output;
            }
        }
        return $output;
    }


}


?>
