<?php


namespace wishlist\divers;

class Bouton {

    public static function itemDelete($item_name) {
        echo '
			<form action="../delete-item/' . $item_name . '" method="POST">
			<div class= "row column align-center medium-6 large-4">
				<button type="submit" class="alert button">
					<div class ="row">
						<div class="columns small-2 fi-trash"></div>
						<div class="columns">Supprimer item</div>
					</div>
				</button>
			</div>
		</form>';
        echo '
		<div class= "row column align-center medium-6 large-4">
			<p class="help-text">Cette action est irréversible</p>
		</div>';

    }

    public static function imageDelete($item_name) {
        echo '
			<form action="../delete-image/' . $item_name . '" method="POST">
				<div class= "row align-center medium-6 large-4">
					<div class="columns small-12 medium-expand">
						<button type="submit" class="alert button">
							<div class ="row">
								<div class="columns small-2 fi-trash large"></div>
								<div class="columns">Supprimer image</div>
							</div>
						</button>
					</div>
				</div>
			</form>';
        echo '
		<div class= "row column align-center medium-6 large-4">
			<p class="help-text">Cette action est irréversible</p>
		</div>';
    }

    public static function compteDelete() {
        echo '
		<form action="delete-compte" method="POST">
			<div class= "row column align-center medium-6 large-4">
				<button type="submit" class="alert button">
					<div class ="row">
						<div class="columns small-2 fi-trash"></div>
						<div class="columns">Supprimer le compte</div>
					</div>
				</button>
			</div>
		</form>';
        echo '
		<div class= "row column align-center medium-6 large-4">
			<p class="help-text">Cette action est irréversible</p>
		</div>';
    }

}