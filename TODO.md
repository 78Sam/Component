V1.0:
- [ ] Finish Tokenizer variant of components
- [ ] Remove validator in favour of parser and rename parser to validator
- [ ] Potentially some testing of stuff
- [ ] Potentially DB changes

And beyond:
- Migrations?
- Better logging?

// Zed settings
//
// For information on how to configure Zed, see the Zed
// documentation: https://zed.dev/docs/configuring-zed
//
// To see all of Zed's default settings without changing your
// custom settings, run the `open default settings` command
// from the command palette or from `Zed` application menu.
{
	"ensure_final_newline_on_save": false,
	"remove_trailing_whitespace_on_save": false,
	"outline_panel": {
	"dock": "left"
	},
	"collaboration_panel": {
	"dock": "left"
	},
	"agent": {
	"dock": "right",
	"favorite_models": [],
	"model_parameters": []
	},
	"git_panel": {
	"dock": "left"
	},
	"minimap": {
	"show": "always"
	},
	"tab_size": 4,
	"hard_tabs": false,
	"tabs": {
	"file_icons": true
	},
	"title_bar": {
	
	},
	"icon_theme": "Catppuccin Latte",
	"project_panel": {
	"dock": "left",
	"auto_fold_dirs": false
	},
	"format_on_save": "off",
	"disable_ai": true,
	"show_edit_predictions": false,
	"theme": "GitHub Dark",
	"ui_font_size": 16,
	"buffer_font_size": 16,
	"languages": {
		"PHP": {
			"language_servers": [
				"intelephense",
			]
		}
	}
}
