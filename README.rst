****************************
FLOW Package to use TMDB API
****************************

This contains the TYPO3 Flow package "TYPO3.Tmdb" to help you with The Movie Database (TMDb) API.

============
Installation
============

1. Just install and activate the package

2. Use the given service

::

	$movies = $this->tmdbService->search('movie', array(
		'query' => 'Florence'
	));
	$this->view->assign('movies', $movies);

====
Tips
====

You can retrive expended information, by adding a third parameter to the search method:

::

	$movies = $this->tmdbService->search('movie', array(
		'query' => 'Florence'
	), TRUE);
	$this->view->assign('movies', $movies);

=======
Roadmap
=======

1. Migration to composer package (to be compatible with the next version of TYPO3 Flow)

2. Adding a cache to avoid calling the webservice to often

3. Adding Viewhelpers to render the return objects