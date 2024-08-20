<?php

namespace Safronik\CodePatterns\Structural;

trait Tree {

	use FluidInterface;

	public object $parent;

	public array $children = [];

	/**
     * Set Parent for current node
     *
	 * @param object $parent
	 */
	protected function setParent( object $parent ): void
    {
		$this->parent = $parent;
	}

	/**
     * Append child for current node
     *
	 * @param mixed $child
	 */
	protected function addChild( mixed $child ): void
    {
        $this->children[] = $child;
        $child->parent    = $this;
	}
    
    /**
     * Get parent for current node. Search up for requested times
     *
     * @param int $iterations
     *
     * @return object|null
     */
    public function getParent( int $iterations = 1 ): ?object
    {
        $iterations--;
        
        return $iterations
            ? $this->parent->getParent( $iterations )
            : $this->parent ?? null;
    }

	/**
     * Returns all children
     *
	 * @return array
     */
	public function getChildren(): array
    {
		return $this->children;
	}

}