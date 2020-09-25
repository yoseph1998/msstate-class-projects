package finch.robot.gui;

import finch.robot.framework.Scheduler;
import finch.robot.input.Keyboard;

import javax.swing.*;
import java.awt.*;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;

/**
 * Created by yosephsa on 9/28/2016.
 */
public class RobotWindow extends JPanel implements Runnable {

    public final int WIDTH = 1080;
    public final int HEIGHT = WIDTH * 9 / 16;
    private final JFrame frame;
    JTextArea textArea;
    JScrollPane scrollPane;
    private float wScale = 1, hScale = 1;
    private boolean running = false;
    private Thread thread;
    private long rate = 20;

    public RobotWindow() {
        super();
        frame = new JFrame("Robot Window");
        frame.setContentPane(this);
        frame.setFocusable(true);
        frame.setSize(WIDTH, HEIGHT);
        frame.setResizable(false);
        frame.setDefaultCloseOperation(WindowConstants.DO_NOTHING_ON_CLOSE);
        frame.addWindowListener(new WindowAdapter() {
            public void windowClosing(WindowEvent e) {
                Scheduler.getInstance().stop();
            }
        });
        this.addKeyListener(Keyboard.getInstence());

        this.setLayout(new GridBagLayout());
        GridBagConstraints c = new GridBagConstraints();

        int fSize = 30;
        JLabel label = new JLabel("Yoseph's Finch Project");
        label.setFont(new Font("Arial", 0, fSize));
        c.gridx = 0;
        c.gridy = 0;
        c.fill = GridBagConstraints.PAGE_START;
        this.add(label, c);

        textArea = new JTextArea();
        textArea.setEditable(false);
        scrollPane = new JScrollPane(textArea);
        scrollPane.setAutoscrolls(true);
        scrollPane.setVerticalScrollBarPolicy(ScrollPaneConstants.VERTICAL_SCROLLBAR_ALWAYS);
        c.gridx = 0;
        c.gridy = 1;

        c.fill = GridBagConstraints.HORIZONTAL;
        c.ipady = HEIGHT - 50 - fSize * 2;
        c.ipadx = WIDTH - 50;
        this.add(scrollPane, c);
        start();
    }

    public void start() {
        running = true;
        if (thread == null) {
            frame.setVisible(true);
            thread = new Thread(this);
            thread.start();
        }
    }

    public void stop() {
        running = false;
    }

    public void end() {
        frame.dispose();
    }

    public void addText(String text) {
        addText(text, true);
    }

    public void addText(String text, boolean breakLine) {
        textArea.append(text);
        if (breakLine)
            textArea.append("\n");
        JScrollBar vertical = scrollPane.getVerticalScrollBar();
        vertical.setValue(vertical.getMaximum());
    }

    public void setText(String text) {
        textArea.setText(text);
    }

    @Override
    public void run() {
        long begin = System.nanoTime();
        long time = 0;
        long wait = 0;
        while (running) {
            time = System.nanoTime();

            wait = rate - (time - begin);

            if (wait < 0)
                wait = 5;

            try {
                thread.wait(wait);
            } catch (Exception e) {
            }
            this.requestFocus();
            Keyboard.getInstence().update();
        }
        end();
    }
}
