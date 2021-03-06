<?php

return [
    'index' => [
        'costs' => [
            'gas' => [
                'title' => 'Gasbesparing',
                'help' => '<p>De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken:</p><p><strong>- vierkante meters te isoleren vloeroppervlakte</strong><br><strong>- type vloerisolatie</strong><br><strong>- voor vloerisolatie wordt ervan uitgegaan dat de aangrenzende ruimtes verwarmd zijn</strong><br><strong>- uw daadwerkelijk energiegebruik*.</strong></p><p>&nbsp;</p><p><span style="box-sizing: border-box; font-size: 10pt;">*Per maatregel is er per woningtype een maximaal mogelijke besparingspercentage opgegeven. Bij vloerisolatie is bijvoorbeeld voor een tussenwoning maximaal 15 % besparing op het daadwerkelijke gasverbruik voor verwarming mogelijk. Hierdoor wordt voorkomen dat de optelsom van alle besparingen boven uw huidige gasverbruik uitkomt.</span></p>',
            ],
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ],
        ],
        'interested-in-improvement' => [
            'title' => 'Uw interesse in deze maatregel',
            'help' => 'Hier ziet u wat u bij “Algemene gegevens” over uw interesse voor Vloerisolatie hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.',
        ],
        'savings-in-euro' => [
            'title' => 'Besparing in €',
            'help' => 'Indicatieve besparing in € per jaar. De gebruikte energietarieven voor gas en elektra worden jaarlijks aan de marktomstandigheden aangepast.',
        ],
        'comparable-rent' => [
            'title' => 'Vergelijkbare rente',
            'help' => '<p>Meer informatie over de vergelijkbare rente kunt u vinden bij Milieucentraal: <a title="Link Milieucentraal" href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/" target="_blank" rel="noopener">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/</a></p>',
        ],
        'indicative-costs' => [
            'title' => 'Indicatieve kosten',
            'help' => 'Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.',
        ],
        'specific-situation' => [
            'title' => 'Toelichting op specifieke situatie',
            'help' => 'Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.',
        ],
    ],

    'crawlspace' => [
        'unknown-error' => [
            'title' => 'Onbekend! Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.',
        ],
    ],
    'comment' => [
        'title' => 'Toelichting op vloerisolatie',
    ],
];
