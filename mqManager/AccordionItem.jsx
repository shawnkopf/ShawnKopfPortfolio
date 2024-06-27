import {
    Accordion,
    AccordionBody,
    AccordionHeader,
    Badge,
    Card,
    DatePicker,
    Flex,
    Grid,
    List,
    ListItem,
    Text,
    Title,
} from "@tremor/react";
import { useEffect, useState } from "react";

export default function AccordionItem({ update, user }) {
    const [data, setData] = useState({
        location: update?.location ?? "",
        updateDate: update?.update_date ?? "",
    });

    return (
        <Accordion>
            <AccordionHeader>
                {update.status} &nbsp;{" "}
                <Badge color="green">{update.update_date}</Badge>
            </AccordionHeader>
            <AccordionBody>
                <Grid numItemsMd={2} className="gap-6 mt-6">
                    <Card className="max-w-2xl">
                        <Title>Update Details</Title>
                        <List>
                            <ListItem>
                                <span>Updated By</span>
                                <span>{update?.user?.name ?? "None"}</span>
                            </ListItem>
                            <ListItem>
                                <span>Update Date</span>
                                <span>{update?.update_date}</span>
                            </ListItem>
                            <ListItem>
                                <span>Location</span>
                                <span>{update?.location}</span>
                            </ListItem>
                        </List>
                    </Card>
                    <Card>
                        <Title>Email Sent</Title>
                        <Text>{update.email_sent ?? "no"}</Text>
                    </Card>
                </Grid>
                <Grid numItemsMd={2} className="gap-6 mt-6">
                    <Card className="max-w-2xl">
                        <Title>Notes</Title>
                        <Text>{update.notes}</Text>
                    </Card>
                    <Card>
                        <Title>Photo</Title>
                        {update.img && (
                            <img src={update.img} alt="Quilt Update" />
                        )}
                    </Card>
                </Grid>
            </AccordionBody>
        </Accordion>
    );
}
