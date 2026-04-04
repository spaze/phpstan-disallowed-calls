<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedKeywordFactory
{

	private const CONTROL_STRUCTURE = 'control structure';

	private const KEYWORD = 'keyword';

	private const KEYWORDS = [
		// https://www.php.net/language.control-structures
		'if' => self::CONTROL_STRUCTURE,
		'else' => self::CONTROL_STRUCTURE,
		'elseif' => self::CONTROL_STRUCTURE,
		'while' => self::CONTROL_STRUCTURE,
		'do-while' => self::CONTROL_STRUCTURE,
		'for' => self::CONTROL_STRUCTURE,
		'foreach' => self::CONTROL_STRUCTURE,
		'break' => self::CONTROL_STRUCTURE,
		'continue' => self::CONTROL_STRUCTURE,
		'switch' => self::CONTROL_STRUCTURE,
		'match' => self::CONTROL_STRUCTURE,
		'declare' => self::CONTROL_STRUCTURE,
		'return' => self::CONTROL_STRUCTURE,
		'require' => self::CONTROL_STRUCTURE,
		'include' => self::CONTROL_STRUCTURE,
		'require_once' => self::CONTROL_STRUCTURE,
		'include_once' => self::CONTROL_STRUCTURE,
		'goto' => self::CONTROL_STRUCTURE,
		// https://www.php.net/language.variables.scope
		'global' => self::KEYWORD,
	];

	private Formatter $formatter;

	private AllowedConfigFactory $allowedConfigFactory;


	public function __construct(Formatter $formatter, AllowedConfigFactory $allowedConfigFactory)
	{
		$this->formatter = $formatter;
		$this->allowedConfigFactory = $allowedConfigFactory;
	}


	/**
	 * @param array<array{controlStructure?:string|list<string>, structure?:string|list<string>, keyword?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string|list<string>}> $config
	 * @return list<DisallowedKeyword>
	 * @throws ShouldNotHappenException
	 */
	public function getDisallowedKeywords(array $config): array
	{
		$disallowedKeywords = [];
		foreach ($config as $disallowed) {
			$keywords = $disallowed['controlStructure'] ?? $disallowed['structure'] ?? $disallowed['keyword'] ?? null;
			unset($disallowed['controlStructure'], $disallowed['structure'], $disallowed['keyword']);
			if (!$keywords) {
				throw new ShouldNotHappenException("Either 'controlStructure', 'structure', or 'keyword' must be set in configuration items");
			}
			$keywords = (array)$keywords;
			try {
				foreach ($keywords as $keyword) {
					if ($keyword === 'else if') {
						throw new ShouldNotHappenException("Use 'elseif' instead of 'else if', because 'else if' is parsed as 'else' followed by 'if' and the behaviour may be unexpected if using 'else if' in the configuration");
					}
					if (!isset(self::KEYWORDS[$keyword])) {
						throw new ShouldNotHappenException(sprintf('%s is not a supported keyword, use one of %s', $keyword, implode(', ', array_keys(self::KEYWORDS))));
					}
					$disallowedKeyword = new DisallowedKeyword(
						$keyword,
						self::KEYWORDS[$keyword],
						$disallowed['message'] ?? null,
						$this->allowedConfigFactory->getConfig($disallowed),
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? []
					);
					$disallowedKeywords[$disallowedKeyword->getKeyword()] = $disallowedKeyword;
				}
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($keywords), $e->getMessage()));
			}
		}
		return array_values($disallowedKeywords);
	}

}
