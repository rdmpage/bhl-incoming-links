# BHL incoming links

Measuring the links to BHL from external sources such as Wikipedia, Catalogue of Life, etc.

## Wikispecies

Using XML dump specieswiki-20220701-pages-articles-multistream.xml I extracted BHL URLs and then used Microcitation Parser to match URLs to BHL PageIDs.

### Problems parsing

Sometimes types had leading `/`:

```
SELECT * FROM specieswiki WHERE type LIKE "/%";
```

```
UPDATE specieswiki SET type = REPLACE(type,"/", "");
```

### Problems matching to BHL

In some cases microparser could not convert URLs into PageIDs. For example, an item was not retrived from the BHl API.

#### item 89591 
Has been removed (was volume `142.d. (1999)` of *Tijdschrift voor entomologie*)

#### item 88509
Removed, not in BHL or BioStor. Not on current Wikispecies page for sources ([Sphaeriusidae](https://species.wikimedia.org/wiki/Sphaeriusidae),[Sphaerius](https://species.wikimedia.org/wiki/Sphaerius),[Sphaerius acaroides](https://species.wikimedia.org/wiki/Sphaerius), but in edit history for Sphaeriusidae https://species.wikimedia.org/w/index.php?title=Sphaeriusidae&diff=10583117&oldid=8832692 as 

> * {{aut|[[Waltl|Waltl, J.]]}} 1838: Beiträge zur nähem naturhistorischen Kenntniß des Unterdonaukreises in Bayern. ''Isis von Oken'', 1838: 250–273. [http://www.biodiversitylibrary.org/item/88509#6 BHL]

#### item 68510

Removed, was `9th ser. v. 11 (1923)` of  *Annals and magazine of natural history*. Link still on Wikispecies [Toxorhina levis](https://species.wikimedia.org/wiki/Toxorhina_levis) as:

> * {{aut|Alexander, C.P.}} 1923: New or little-known Tipulidae (Diptera). - XIII. Australasian Species. ''Annals and magazine of natural history'' (9), 11: 97–111. [http://www.biodiversitylibrary.org/item/68510#6 BHL] [https://web.archive.org/web/20110724201629/http://www.bugz.org.nz//WebForms/SearchForm.aspx BUGZ]
{{Edwards, 1923}}

#### item 41450

Removed, was item in [Analyse des familles des plantes, avec l'indication des principaux genres qui s'y rattachent](https://www.biodiversitylibrary.org/title/443) which has moved. No links with item so likely to be linked to a page that is also now missing from BHL.

#### item 25953

In BHL, not retrieved by Microparser (why?) Histoire naturelle des poissons t.18. No direct links, so likely a PageID in Wikispecies.

#### item 25911

In BHL, not retrieved by Microparser (why?) Histoire naturelle des poissons t.7. No direct links, so likely a PageID in Wikispecies.


## Wikidata

