import {
    AccordionList,
} from "@tremor/react";
import AccordionItem from "@/Components/AccordionItem.jsx";


export default function QuiltAccordion({quilt, user})
{
    return(
        <AccordionList className="mx-auto">
            {quilt?.quilt_updates.map((update) => {
                return(
                <AccordionItem update={update} key={update.id} />
                )
            })}
        </AccordionList>
    )
}
