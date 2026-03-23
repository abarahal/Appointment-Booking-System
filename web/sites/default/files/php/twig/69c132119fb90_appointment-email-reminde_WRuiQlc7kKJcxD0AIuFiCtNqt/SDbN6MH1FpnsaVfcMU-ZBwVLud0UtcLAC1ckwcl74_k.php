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

/* modules/custom/appointment/templates/email/appointment-email-reminder.html.twig */
class __TwigTemplate_628ad66ea24b4efc2c07b57906176723 extends Template
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
        // line 11
        yield "<div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;\">
  <div style=\"background: #17a2b8; color: #fff; padding: 20px; border-radius: 6px 6px 0 0; text-align: center;\">
    <h1 style=\"margin: 0; font-size: 22px;\">&#128276; ";
        // line 13
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Rappel de votre rendez-vous"));
        yield "</h1>
  </div>

  <div style=\"border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 6px 6px;\">
    <p>";
        // line 17
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Bonjour"));
        yield " <strong>";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_name", [], "any", false, false, true, 17), "value", [], "any", false, false, true, 17), "html", null, true);
        yield "</strong>,</p>

    <p>";
        // line 19
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Nous vous rappelons que vous avez un rendez-vous prévu prochainement."));
        yield "</p>

    <div style=\"background: #e8f4f8; border-left: 4px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 0 4px 4px 0;\">
      <strong style=\"font-size: 18px;\">";
        // line 22
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["date_label"] ?? null), "html", null, true);
        yield "</strong>
      ";
        // line 23
        if ((($tmp = ($context["hours_until"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 24
            yield "        <br><span style=\"color: #666;\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Dans environ @hours heures", ["@hours" => ($context["hours_until"] ?? null)]));
            yield "</span>
      ";
        }
        // line 26
        yield "    </div>

    <table style=\"width: 100%; border-collapse: collapse; margin: 15px 0;\">
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666; width: 40%;\">";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Référence"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\"><strong>#";
        // line 31
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "id", [], "any", false, false, true, 31), "value", [], "any", false, false, true, 31), "html", null, true);
        yield "</strong></td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 34
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Agence"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\">";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "agency", [], "any", false, false, true, 35), "entity", [], "any", false, false, true, 35), "label", [], "any", false, false, true, 35), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 38
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Conseiller"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\">";
        // line 39
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "adviser_name", [], "any", false, false, true, 39), "value", [], "any", false, false, true, 39), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 42
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Type"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\">";
        // line 43
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "appointment_type", [], "any", false, false, true, 43), "entity", [], "any", false, false, true, 43), "label", [], "any", false, false, true, 43), "html", null, true);
        yield "</td>
      </tr>
    </table>

    <p style=\"text-align: center; margin: 25px 0;\">
      <a href=\"";
        // line 48
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["manage_url"] ?? null), "html", null, true);
        yield "\" style=\"background: #0074bd; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block;\">
        ";
        // line 49
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Gérer mon rendez-vous"));
        yield "
      </a>
    </p>

    <p style=\"color: #999; font-size: 12px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;\">
      ";
        // line 54
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Ce message a été envoyé automatiquement par"));
        yield " ";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["site_name"] ?? null), "html", null, true);
        yield ".
    </p>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["appointment", "date_label", "hours_until", "manage_url", "site_name"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/appointment/templates/email/appointment-email-reminder.html.twig";
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
        return array (  140 => 54,  132 => 49,  128 => 48,  120 => 43,  116 => 42,  110 => 39,  106 => 38,  100 => 35,  96 => 34,  90 => 31,  86 => 30,  80 => 26,  74 => 24,  72 => 23,  68 => 22,  62 => 19,  55 => 17,  48 => 13,  44 => 11,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/appointment/templates/email/appointment-email-reminder.html.twig", "/Users/void/schedulehub_project/web/modules/custom/appointment/templates/email/appointment-email-reminder.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 23];
        static $filters = ["t" => 13, "escape" => 17];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['t', 'escape'],
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
