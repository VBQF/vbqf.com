# Ajout des icones de liaison des réseaux sociaux

Dans le package de thème JA-Elastica, le positionnement social est absent du fichier templateDetails.xml.

Pour le faire fonctionner, il suffit d'ajouter dans le bloc "positions" la directive :

	<position>social</position>


Le paramétrage du module s'effectue ensuite par le biais d'un nouveau module de contenu HTML personnalisé. La syntaxe est un liste d'éléments comme suit :

	<ul class="ja-social">
	<li class="social-twitter"><a href="#" title="Twitter">Twitter</a></li>
	<li class="social-facebook"><a href="#" title="Facebook">Facebook</a></li>
	<li class="social-gplus"><a href="#" title="Google+">Google+</a></li>
	<li class="social-rss"><a href="#" title="RSS">RSS</a></li>
	</ul>

