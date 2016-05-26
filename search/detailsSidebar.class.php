<?php

/**
 * Class detailsSidebar
 *
 * Represents a sidebar that can be displayed. Holds the necessary information to display a sidebar of artifacts
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class detailsSidebar {

    /**
     * @var array
     */
    private $resultSet = array();
    /**
     * @var artifact
     */
    private $activeArtifact = null;
    /**
     * @var string
     */
    private $barTitle = "";
    /**
     * @var string
     */
    private $searchURL = "";
	
	public function __construct($resultSet, $activeArtifact, $title, $searchURL){
		$this->resultSet = $resultSet;
		$this->activeArtifact = $activeArtifact;
		$this->barTitle = $title;
		$this->searchURL = $searchURL;
	}

    /**
     * Displays the sidebar HTML
     * @return bool True if the sidebar could be displayed, False otherwise
     */
    public function display(){
		if(count($this->resultSet) > 1){
			echo '<div class="sideResultsOuter">
						<div class="sideResults">
						<div class="sideResultsTitle"><h3>' . $this->barTitle . '</h3></div>
						<ul>';
			foreach($this->resultSet as $prevArtifact){
				if($prevArtifact->getID() != $this->activeArtifact->getID()){
					$name = (strlen($prevArtifact->getName()) > 53) ? substr($prevArtifact->getName(), 0, 50).'...' : $prevArtifact->getName();
					echo '		<li>';
					echo '			<a href="details.php?a=' . $prevArtifact->getAccessionNumber() . '&rel=1"><img src="' . $prevArtifact->getThumbImage(artifact::SIDEBAR_CONTEXT) . '"></a><br>';
					echo '			<a href="details.php?a=' . $prevArtifact->getAccessionNumber() . '&rel=1">' . $name . ' <br>(' . $prevArtifact->getAccessionNumber() . ')</a>';
					echo '		</li>';
				}
			}
			echo '			</ul>
							<a class="searchURL" href="' . $this->searchURL . '">[ View More ]</a>
						</div>
					</div>';
			return true;
		}
		return false;
	}
}

?>