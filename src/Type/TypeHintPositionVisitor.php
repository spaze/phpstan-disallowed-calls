<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Type;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\UnionType;
use PhpParser\NodeVisitorAbstract;

/**
 * Marks type nodes with custom attributes indicating whether they appear as
 * parameter type hints or return type hints, so rules can detect this without
 * relying on PHP-Parser's parent/node-connecting attributes.
 */
class TypeHintPositionVisitor extends NodeVisitorAbstract
{

	public const ATTRIBUTE_IN_PARAM_TYPE = self::class . '_inParamType';
	public const ATTRIBUTE_IN_RETURN_TYPE = self::class . '_inReturnType';


	public function enterNode(Node $node)
	{
		if ($node instanceof Param && $node->type !== null) {
			$this->markTypeNode($node->type, self::ATTRIBUTE_IN_PARAM_TYPE);
		} elseif ($node instanceof FunctionLike && $node->getReturnType() !== null) {
			$this->markTypeNode($node->getReturnType(), self::ATTRIBUTE_IN_RETURN_TYPE);
		}
		return null;
	}


	private function markTypeNode(Node $typeNode, string $attribute): void
	{
		$typeNode->setAttribute($attribute, true);
		if ($typeNode instanceof NullableType) {
			if ($typeNode->type instanceof FullyQualified) {
				$typeNode->type->setAttribute($attribute, true);
			}
		} elseif ($typeNode instanceof UnionType || $typeNode instanceof IntersectionType) {
			foreach ($typeNode->types as $type) {
				if ($type instanceof FullyQualified) {
					$type->setAttribute($attribute, true);
				}
			}
		}
	}

}
