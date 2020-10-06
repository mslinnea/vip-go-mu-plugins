<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Sniffer that looks for JETPACK_MASTER_USER constant usage
 */
class Jetpack_Sniffs_MasterUserConstantSniff implements PHP_CodeSniffer_Sniff {
	/**
	 * Returns the token types that this sniff is interested in.
	 *
	 * @return array(int)
	 */
	public function register() {
		return array( T_STRING );
	}


	/**
	 * Processes the tokens that this sniff is interested in.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file where the token was found.
	 * @param int                  $stack_ptr The position in the stack where the token was found.
	 */
	public function process( PHP_CodeSniffer_File $phpcs_file, $stack_ptr ) {

		$tokens = $phpcs_file->getTokens();

		if ( 'JETPACK_MASTER_USER' === $tokens[ $stack_ptr ]['content'] ) {
			$phpcs_file->addWarning(
				'JETPACK_MASTER_USER constant should not be used. Use the blog token to make requests instead, or use the current user token when needed.',
				$stack_ptr,
				'ShouldNotBeUsed'
			);
		}

	}

}
