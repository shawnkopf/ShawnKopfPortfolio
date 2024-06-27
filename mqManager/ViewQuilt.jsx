import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import { Head } from "@inertiajs/react";
import {
    Grid,
    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    Text,
    Title,
    Card,
} from "@tremor/react";
import QuiltAccordion from "@/Components/QuiltAccordion.jsx";
import QuiltInfoList from "@/Components/QuiltInfoList.jsx";
import QuiltDetailsList from "@/Components/QuiltDetailsList.jsx";
import NotesBox from "@/Components/NotesBox.jsx";
import QuiltUpdateModal from "@/Components/QuiltUpdateModal.jsx";
import { useEffect, useState } from "react";

export default function ViewQuilt({ auth, quiltProp, emailCopies }) {
    const [openModal, setOpenModal] = useState(false);
    const [quilt, setQuilt] = useState(quiltProp);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    View Quilt: {quilt?.id ?? ""}
                    <button
                        type="button"
                        onClick={() => setOpenModal(true)}
                        className="text-white ml-3 text-sm bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg px-2 py-1.5 text-center"
                    >
                        Update Status
                    </button>
                    <button
                        type="button"
                        onClick={() =>
                            window.open(
                                "https://admin.shopify.com/store/machinequilting/orders/" +
                                    quilt?.shopify_order_id ?? "",
                                "_blank"
                            )
                        }
                        className="text-white ml-3 text-sm bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg px-2 py-1.5 text-center"
                    >
                        See in Shopify
                    </button>
                    <button
                        type="button"
                        onClick={() =>
                            window.open("printQuilt/" + quilt.id, "_blank")
                        }
                        className="text-white ml-3 text-sm bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg px-2 py-1.5 text-center"
                    >
                        Print Tag
                    </button>
                </h2>
            }
        >
            <Head title="Dashboard" />
            <QuiltUpdateModal
                auth={auth}
                setQuilt={setQuilt}
                openModal={openModal}
                setOpenModal={setOpenModal}
                user={auth.user}
                emailCopies={emailCopies}
                quilt={quilt}
            />
            <main className="px-4 py-12">
                <Title>Sundance Quilting</Title>
                <Text>MqManager v2</Text>

                <TabGroup className="mt-6">
                    <TabList>
                        <Tab>Overview</Tab>
                        <Tab>Extra Space</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel>
                            <Grid
                                numItemsMd={2}
                                numItemsLg={2}
                                className="gap-6 mt-6"
                            >
                                <QuiltInfoList quilt={quilt} />
                                <QuiltDetailsList
                                    quilt={quilt}
                                ></QuiltDetailsList>
                            </Grid>
                            <div className="mt-6">
                                <NotesBox
                                    title="Customer Notes"
                                    text={quilt?.order_note ?? ""}
                                />
                            </div>
                            <div className="mt-6">
                                <QuiltAccordion
                                    user={auth.user}
                                    quilt={quilt}
                                />
                            </div>
                        </TabPanel>

                        <TabPanel>
                            <div className="mt-6">
                                <Card></Card>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </main>
        </AuthenticatedLayout>
    );
}
