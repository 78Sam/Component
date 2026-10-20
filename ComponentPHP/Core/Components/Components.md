Ok probs gonna have to cave and make some changes to Components, ideas so far:
- Give up my unique !@ syntax in favour of {{}}
- Add if statements and loops, but do them similar to what I've got with sockets, e.g.
    ```php
    $conditionals = ['conditionVariable'];
    $sockets = ['conditionVariable' => SomeIfStatementClassConditionalThing];
    $component->fill('conditionVariable', true);
    ```
    And something similar for loops, but essentially they both do their thing at `render()`
- Split out the htmlSpecialChars thing into `HTMLComponent extends AbstractComponent` and add an `SQLComponent`
