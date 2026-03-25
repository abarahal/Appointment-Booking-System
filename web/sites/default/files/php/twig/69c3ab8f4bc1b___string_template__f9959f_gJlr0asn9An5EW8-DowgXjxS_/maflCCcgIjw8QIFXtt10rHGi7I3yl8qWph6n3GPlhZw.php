<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* __string_template__f9959f6f05da0d9840179f2d5f3b8779 */
class __TwigTemplate_d25a98e48c08f928be74c46d5a98bc72 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 2
        yield "                <div class=\"appointment-admin-filters\">
                  <form method=\"get\" action=\"";
        // line 3
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["list_url"] ?? null), "html", null, true);
        yield "\">
                    <div class=\"appointment-filters-inline\">
                      <label>";
        // line 5
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Status"));
        yield "
                        <select name=\"status\">
                          ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["status_options"] ?? null));
        foreach ($context['_seq'] as $context["val"] => $context["label"]) {
            // line 8
            yield "                            <option value=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["val"], "html", null, true);
            yield "\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(((($context["val"] == ($context["current_status"] ?? null))) ? (" selected") : ("")));
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["label"], "html", null, true);
            yield "</option>
                          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['val'], $context['label'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        yield "                        </select>
                      </label>
                      <label>";
        // line 12
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Agency"));
        yield "
                        <select name=\"agency\">
                          ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["agency_options"] ?? null));
        foreach ($context['_seq'] as $context["val"] => $context["label"]) {
            // line 15
            yield "                            <option value=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["val"], "html", null, true);
            yield "\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(((($context["val"] == ($context["current_agency"] ?? null))) ? (" selected") : ("")));
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["label"], "html", null, true);
            yield "</option>
                          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['val'], $context['label'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        yield "                        </select>
                      </label>
                      <label>";
        // line 19
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Adviser"));
        yield "
                        <select name=\"adviser\">
                          ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["adviser_options"] ?? null));
        foreach ($context['_seq'] as $context["val"] => $context["label"]) {
            // line 22
            yield "                            <option value=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["val"], "html", null, true);
            yield "\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(((($context["val"] == ($context["current_adviser"] ?? null))) ? (" selected") : ("")));
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["label"], "html", null, true);
            yield "</option>
                          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['val'], $context['label'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        yield "                        </select>
                      </label>
                      <label>";
        // line 26
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("From"));
        yield "
                        <input type=\"date\" name=\"date_from\" value=\"";
        // line 27
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["date_from"] ?? null), "html", null, true);
        yield "\">
                      </label>
                      <label>";
        // line 29
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("To"));
        yield "
                        <input type=\"date\" name=\"date_to\" value=\"";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["date_to"] ?? null), "html", null, true);
        yield "\">
                      </label>
                      <button type=\"submit\" class=\"button button--primary\">";
        // line 32
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Filter"));
        yield "</button>
                      <a href=\"";
        // line 33
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["list_url"] ?? null), "html", null, true);
        yield "\" class=\"button\">";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Reset"));
        yield "</a>
                    </div>
                  </form>
                </div>";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["list_url", "status_options", "current_status", "agency_options", "current_agency", "adviser_options", "current_adviser", "date_from", "date_to"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "__string_template__f9959f6f05da0d9840179f2d5f3b8779";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  152 => 33,  148 => 32,  143 => 30,  139 => 29,  134 => 27,  130 => 26,  126 => 24,  113 => 22,  109 => 21,  104 => 19,  100 => 17,  87 => 15,  83 => 14,  78 => 12,  74 => 10,  61 => 8,  57 => 7,  52 => 5,  47 => 3,  44 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "__string_template__f9959f6f05da0d9840179f2d5f3b8779", "");
    }
    
    public function checkSecurity()
    {
        static $tags = ["for" => 7];
        static $filters = ["escape" => 3, "t" => 5];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['for'],
                ['escape', 't'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
